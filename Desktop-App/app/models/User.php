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
            $stmt = $this->conn->prepare("SELECT id, name, email, password, role, approval_status, IFNULL(two_factor_enabled,0) as two_factor_enabled, avatar, google_avatar FROM users WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Minimal auth logging without sensitive data
                error_log("Login attempt for: $email; role={$user['role']}; approval={$user['approval_status']}");
                
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
                        error_log("Login successful for $email: hashed password verified");
                    } else {
                        error_log("Login failed for $email: Hashed password mismatch");
                    }
                } else {
                    // Plain text password (legacy) - direct comparison
                    if ($password === $user['password']) {
                        $passwordMatch = true;
                        error_log("Login successful for $email: legacy plaintext password matched");
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
            
            // Devotee and priest registrations must be approved by management.
            $approvalStatus = in_array($role, ['devotee', 'priest']) ? 'pending' : 'approved';
            
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role, phone, approval_status) VALUES (?, ?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ssssss", $name, $email, $hash, $role, $phone, $approvalStatus);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            // Log registration
            if ($success && $approvalStatus === 'pending') {
                $this->logRegistration($insertId, 'registered', null, "User registered as $role");
                $this->notifyManagementForApproval([
                    'id' => $insertId,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ]);
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

                // Send approval/rejection email confirmation (dev log mode)
                if ($user && !empty($user['email']) && function_exists('sendEmailNotification')) {
                    $subject = $status === 'approved'
                        ? 'Kovil Registration Approved'
                        : 'Kovil Registration Update';
                    $emailMessage = $status === 'approved'
                        ? "Hello {$user['name']},\n\nYour registration as {$user['role']} has been approved. You can now login to the system."
                        : "Hello {$user['name']},\n\nYour registration has been rejected.\nReason: {$remarks}";
                    sendEmailNotification($user['email'], $subject, $emailMessage, 'registration_approval');
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

    public function notifyManagementForApproval($user) {
        try {
            $managementUsers = $this->getAllManagement();
            if (!$managementUsers) {
                return;
            }
            while ($manager = $managementUsers->fetch_assoc()) {
                if (!empty($manager['email']) && function_exists('sendEmailNotification')) {
                    $subject = "Approval required: new {$user['role']} registration";
                    $message = "A new user registration is pending approval.\n\n"
                        . "Name: {$user['name']}\n"
                        . "Email: {$user['email']}\n"
                        . "Role: {$user['role']}\n\n"
                        . "Please review under Approve Registration in the admin panel.";
                    sendEmailNotification($manager['email'], $subject, $message, 'registration_approval_request');
                }
            }
        } catch (Exception $e) {
            error_log("Notify management approval request error: " . $e->getMessage());
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

    /**
     * Get approved devotees (for admin reassignment flows).
     */
    public function getApprovedDevotees() {
        $stmt = $this->conn->prepare("
            SELECT id, name, email, phone
            FROM users
            WHERE role = 'devotee' AND approval_status = 'approved'
            ORDER BY name ASC
        ");
        if (!$stmt) {
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Create password reset token and return raw token.
     */
    public function createPasswordResetToken($email, $expiryMinutes = 30) {
        $user = $this->getUserWithPasswordByEmail($email);
        if (!$user || $user['approval_status'] !== 'approved') {
            return ['success' => false, 'message' => 'If your email exists, a reset link will be sent.'];
        }

        $rawToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $stmt = $this->conn->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE))");
        if (!$stmt) {
            error_log("Create reset token prepare failed: " . $this->conn->error);
            return ['success' => false, 'message' => 'Failed to start password reset'];
        }
        $stmt->bind_param("isi", $user['id'], $tokenHash, $expiryMinutes);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            return ['success' => false, 'message' => 'Failed to start password reset'];
        }

        return ['success' => true, 'token' => $rawToken, 'user' => $user];
    }

    /**
     * Validate password reset token and return reset row + user.
     */
    public function getValidPasswordReset($rawToken) {
        $tokenHash = hash('sha256', (string) $rawToken);
        $stmt = $this->conn->prepare("
            SELECT pr.id, pr.user_id, pr.expires_at, pr.used_at, u.email, u.name
            FROM password_resets pr
            JOIN users u ON u.id = pr.user_id
            WHERE pr.token_hash = ?
            LIMIT 1
        ");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $tokenHash);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row || !empty($row['used_at']) || strtotime($row['expires_at']) < time()) {
            return false;
        }

        return $row;
    }

    /**
     * Reset password using raw token and mark token as used.
     */
    public function resetPasswordWithToken($rawToken, $newPassword) {
        $resetRow = $this->getValidPasswordReset($rawToken);
        if (!$resetRow) {
            return ['success' => false, 'message' => 'Invalid or expired reset link'];
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->conn->begin_transaction();
        try {
            $updatePasswordStmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updatePasswordStmt->bind_param("si", $hash, $resetRow['user_id']);
            $pwUpdated = $updatePasswordStmt->execute();
            $updatePasswordStmt->close();

            if (!$pwUpdated) {
                throw new Exception('Failed to update password');
            }

            $markUsedStmt = $this->conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
            $markUsedStmt->bind_param("i", $resetRow['id']);
            $markUsedStmt->execute();
            $markUsedStmt->close();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Password reset successful'];
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Reset password error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Could not reset password'];
        }
    }

    /**
     * Update user details
     */
    public function updateUser($id, $name, $email, $phone, $role, $approvalStatus) {
        try {
            $check = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check->bind_param("si", $email, $id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $check->close();
                return ['success' => false, 'message' => 'Email is already used by another account'];
            }
            $check->close();

            $stmt = $this->conn->prepare("UPDATE users SET name=?, email=?, phone=?, role=?, approval_status=? WHERE id=?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $phone = $phone ?: null;
            $stmt->bind_param("sssssi", $name, $email, $phone, $role, $approvalStatus, $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok
                ? ['success' => true, 'message' => 'User updated successfully']
                : ['success' => false, 'message' => 'Failed to update user'];
        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating user'];
        }
    }

    /**
     * Delete a user by ID
     */
    public function deleteUser($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (Exception $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get full profile data for the settings page.
     */
    public function getProfileById(int $id): ?array {
        $stmt = $this->conn->prepare("
            SELECT id, name, email, phone, role, approval_status,
                   google_id, google_avatar, avatar,
                   IFNULL(two_factor_enabled, 0) AS two_factor_enabled,
                   created_at,
                   (password IS NOT NULL AND password != '') AS has_password
            FROM users WHERE id = ? LIMIT 1
        ");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    /**
     * Update a user's own profile (name, email, phone).
     */
    public function updateProfile(int $id, string $name, string $email, ?string $phone): array {
        $check = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $check->close();
            return ['success' => false, 'message' => 'That email is already used by another account.'];
        }
        $check->close();

        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        if (!$stmt) return ['success' => false, 'message' => 'Update failed.'];
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok
            ? ['success' => true,  'message' => 'Profile updated successfully.']
            : ['success' => false, 'message' => 'Failed to save changes.'];
    }

    /**
     * Set a password for a user (used by Google-only accounts creating a password for the first time).
     */
    public function setPassword(int $id, string $newPassword): array {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        if (!$stmt) return ['success' => false, 'message' => 'Update failed.'];
        $stmt->bind_param("si", $hash, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok
            ? ['success' => true,  'message' => 'Password created. You can now log in with your email and password.']
            : ['success' => false, 'message' => 'Failed to save password. Please try again.'];
    }

    /**
     * Change password — verifies the current password first.
     */
    public function changePassword(int $id, string $currentPassword, string $newPassword): array {
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
        if (!$stmt) return ['success' => false, 'message' => 'Failed.'];
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (empty($row['password'])) {
            return ['success' => false, 'message' => 'No existing password found.'];
        }

        $isLegacy = strpos($row['password'], '$2y$') !== 0;
        $matches  = $isLegacy
            ? ($currentPassword === $row['password'])
            : password_verify($currentPassword, $row['password']);

        if (!$matches) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }

        return $this->setPassword($id, $newPassword);
    }

    /**
     * Save custom avatar filename for a user.
     */
    public function updateAvatar(int $id, ?string $avatarFilename): array {
        $stmt = $this->conn->prepare("UPDATE users SET avatar = ?, updated_at = NOW() WHERE id = ?");
        if (!$stmt) return ['success' => false, 'message' => 'Update failed.'];
        $stmt->bind_param("si", $avatarFilename, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok
            ? ['success' => true,  'message' => 'Avatar updated.']
            : ['success' => false, 'message' => 'Failed to update avatar.'];
    }

    /**
     * Enable or disable 2FA for a user.
     */
    public function toggle2FA(int $id, bool $enabled): array {
        $val  = $enabled ? 1 : 0;
        $stmt = $this->conn->prepare("UPDATE users SET two_factor_enabled = ?, updated_at = NOW() WHERE id = ?");
        if (!$stmt) return ['success' => false];
        $stmt->bind_param("ii", $val, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return ['success' => $ok];
    }

    /**
     * Find an existing user by google_id or email, or create a new auto-approved devotee.
     * Used during Google OAuth sign-in.
     */
    public function findOrCreateGoogleUser(array $googleData): ?array {
        $googleId = $googleData['google_id'];
        $email    = $googleData['email'] ?? '';
        $name     = $googleData['name'] ?? 'Devotee';
        $avatar   = $googleData['avatar'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("findOrCreateGoogleUser: invalid email from Google: $email");
            return null;
        }

        // 1. Find by google_id
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, role, approval_status, google_avatar, avatar FROM users WHERE google_id = ? LIMIT 1"
        );
        $stmt->bind_param("s", $googleId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user) {
            // Update avatar if it changed
            if ($user['google_avatar'] !== $avatar) {
                $upd = $this->conn->prepare("UPDATE users SET google_avatar = ?, updated_at = NOW() WHERE id = ?");
                $upd->bind_param("si", $avatar, $user['id']);
                $upd->execute();
                $upd->close();
                $user['google_avatar'] = $avatar;
            }
            return $user;
        }

        // 2. Find by email — link Google account to existing user
        $stmt2 = $this->conn->prepare(
            "SELECT id, name, email, role, approval_status, avatar FROM users WHERE email = ? LIMIT 1"
        );
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $existing = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();

        if ($existing) {
            $link = $this->conn->prepare(
                "UPDATE users SET google_id = ?, google_avatar = ?, updated_at = NOW() WHERE id = ?"
            );
            $link->bind_param("ssi", $googleId, $avatar, $existing['id']);
            $link->execute();
            $link->close();
            $existing['google_avatar'] = $avatar;
            return $existing;
        }

        // 3. Create new devotee — pending approval (admin must approve before login)
        $role   = 'devotee';
        $status = 'pending';
        $ins = $this->conn->prepare(
            "INSERT INTO users (name, email, google_id, google_avatar, role, approval_status) VALUES (?, ?, ?, ?, ?, ?)"
        );
        if (!$ins) {
            error_log("findOrCreateGoogleUser insert prepare failed: " . $this->conn->error);
            return null;
        }
        $ins->bind_param("ssssss", $name, $email, $googleId, $avatar, $role, $status);
        $ok    = $ins->execute();
        $newId = $ins->insert_id;
        $ins->close();

        if (!$ok) {
            return null;
        }

        $this->logRegistration($newId, 'registered', null, 'Registered via Google OAuth');

        return [
            'id'              => $newId,
            'name'            => $name,
            'email'           => $email,
            'role'            => $role,
            'approval_status' => $status,
            'google_avatar'   => $avatar,
            '_is_new'         => true, // triggers onboarding redirect
        ];
    }

    /**
     * Save phone (and optionally name) collected during Google onboarding.
     */
    public function saveGoogleOnboarding(int $id, string $name, string $phone): bool {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("ssi", $name, $phone, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    private function getUserWithPasswordByEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, name, email, password, role, approval_status FROM users WHERE email = ?");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }
}