<?php
require_once __DIR__ . "/../../config/database.php";

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Authenticate user with email and password
     */
    public function login($email, $password) {
        try {
            // First, fetch user by email only (check approval separately)
            $stmt = $this->conn->prepare("SELECT id, name, email, password, role, approval_status FROM users WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Debug logging
                error_log("Login attempt for: $email");
                error_log("User role: {$user['role']}");
                error_log("Approval status: {$user['approval_status']}");
                error_log("Password entered: $password");
                error_log("Password stored: " . $user['password']);
                
                // Check approval status first
                if ($user['approval_status'] !== 'approved') {
                    error_log("Login failed for $email: Account not approved (status: {$user['approval_status']})");
                    $stmt->close();
                    return ['status' => 'pending', 'approval_status' => $user['approval_status']];
                }
                
                $passwordMatch = false;
                
                // Check if password is hashed (starts with $2y$)
                if (strpos($user['password'], '$2y$') === 0) {
                    // Hashed password - use password_verify
                    if (password_verify($password, $user['password'])) {
                        $passwordMatch = true;
                        error_log("Login successful for $email: Hashed password verified");
                    } else {
                        error_log("Login failed for $email: Hashed password mismatch");
                    }
                } else {
                    // Plain text password (legacy) - direct comparison
                    if ($password === $user['password']) {
                        $passwordMatch = true;
                        error_log("Login successful for $email: Plain text password matched");
                    } else {
                        error_log("Login failed for $email: Plain text password mismatch");
                    }
                }
                
                if ($passwordMatch) {
                    $stmt->close();
                    unset($user['password']); // Remove password from returned data
                    return $user;
                } else {
                    $stmt->close();
                    return false;
                }
            } else {
                // User not found
                error_log("Login failed: No user found with email $email");
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            if (isset($stmt) && $stmt) {
                $stmt->close();
            }
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Register a new user with phone number and approval status
     */
    public function register($name, $email, $password, $role = 'devotee', $phone = null) {
        try {
            // Check if email already exists
            $checkStmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $checkStmt->close();
                return ["success" => false, "message" => "Email already registered"];
            }
            $checkStmt->close();
            
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // For priest and management roles, set as pending approval
            $approvalStatus = in_array($role, ['priest', 'management']) ? 'pending' : 'approved';
            
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role, phone, approval_status) VALUES (?, ?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ssssss", $name, $email, $hash, $role, $phone, $approvalStatus);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            // Log registration
            if ($success && in_array($role, ['priest', 'management'])) {
                $this->logRegistration($insertId, 'registered', null, "User registered as $role");
            }
            
            return $success ? 
                ["success" => true, "message" => $approvalStatus === 'pending' ? "Registration successful! Please wait for admin approval." : "Registration successful", "user_id" => $insertId] :
                ["success" => false, "message" => "Registration failed. Please try again."];
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred during registration"];
        }
    }

    /**
     * Get all pending registrations awaiting approval
     */
    public function getPendingRegistrations() {
        $result = $this->conn->query("
            SELECT id, name, email, phone, role, created_at 
            FROM users 
            WHERE approval_status = 'pending' 
            ORDER BY created_at DESC
        ");
        return $result;
    }

    /**
     * Approve or reject user registration
     */
    public function updateApprovalStatus($userId, $status, $approvedBy, $remarks = '') {
        try {
            if (!in_array($status, ['approved', 'rejected'])) {
                return ["success" => false, "message" => "Invalid status"];
            }

            $stmt = $this->conn->prepare("UPDATE users SET approval_status = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("si", $status, $userId);
            $success = $stmt->execute();
            $stmt->close();

            // Log the approval/rejection
            if ($success) {
                $this->logRegistration($userId, $status, $approvedBy, $remarks);
                
                // Send SMS notification
                $user = $this->getById($userId);
                if ($user && $user['phone']) {
                    $message = $status === 'approved' 
                        ? "Your registration as {$user['role']} has been approved. You can now login."
                        : "Your registration has been rejected. Reason: $remarks";
                    
                    sendSMS($user['phone'], $message, 'registration_approval');
                }
            }
            
            return $success ? 
                ["success" => true, "message" => "User $status successfully"] :
                ["success" => false, "message" => "Failed to update status"];
        } catch (Exception $e) {
            error_log("Update approval status error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred"];
        }
    }

    /**
     * Get all users with their details
     */
    public function getAllUsers() {
        $result = $this->conn->query("
            SELECT id, name, email, phone, role, approval_status, created_at 
            FROM users 
            ORDER BY created_at DESC
        ");
        return $result;
    }

    /**
     * Log registration actions
     */
    private function logRegistration($userId, $action, $performedBy = null, $remarks = '') {
        try {
            $stmt = $this->conn->prepare("INSERT INTO registration_logs (user_id, action, performed_by, remarks) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("isis", $userId, $action, $performedBy, $remarks);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            error_log("Log registration error: " . $e->getMessage());
        }
    }

    /**
     * Validate phone number format
     */
    public function validatePhone($phone) {
        // Accept 10-digit Indian numbers or international format
        return preg_match('/^[0-9]{10}$|^\+[0-9]{1,3}[0-9]{10}$/', $phone);
    }

    /**
     * Get user by ID with phone
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, email, phone, role, approval_status, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, name, email, role, approval_status, phone, created_at FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    /**
     * Get all priests
     */
    public function getAllPriests() {
        $result = $this->conn->query("SELECT id, name, email FROM users WHERE role = 'priest' ORDER BY name ASC");
        return $result;
    }

    /**
     * Get all management users
     */
    public function getAllManagement() {
        $result = $this->conn->query("SELECT id, name, email FROM users WHERE role = 'management' ORDER BY name ASC");
        return $result;
    }
}