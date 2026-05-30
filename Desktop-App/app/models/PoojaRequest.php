<?php
require_once __DIR__ . "/../../config/database.php";

class PoojaRequest {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Create a new pooja request
     */
    public function create($user_id, $pooja_name, $preferred_date, $preferred_time_slot = null, $special_requests = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO pooja_requests (user_id, pooja_name, preferred_date, preferred_time_slot, special_requests) VALUES (?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("issss", $user_id, $pooja_name, $preferred_date, $preferred_time_slot, $special_requests);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $success ? 
                ["success" => true, "message" => "Pooja request submitted successfully", "id" => $insertId] :
                ["success" => false, "message" => "Failed to submit pooja request"];
        } catch (Exception $e) {
            error_log("Pooja request error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred while submitting request"];
        }
    }

    /**
     * Get all pooja requests
     */
    public function getAll() {
        $result = $this->conn->query("SELECT pr.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
                                     FROM pooja_requests pr 
                                     JOIN users u ON pr.user_id = u.id 
                                     ORDER BY pr.created_at DESC");
        return $result;
    }

    /**
     * Get pooja requests by user ID
     */
    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM pooja_requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Get pooja request by ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT pr.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
                                     FROM pooja_requests pr 
                                     JOIN users u ON pr.user_id = u.id 
                                     WHERE pr.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();
        $stmt->close();
        return $request;
    }

    /**
     * Update pooja request status
     */
    public function updateStatus($id, $status, $admin_remarks = null) {
        try {
            $stmt = $this->conn->prepare("UPDATE pooja_requests SET status = ?, admin_remarks = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ssi", $status, $admin_remarks, $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Update pooja request error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a pooja request
     */
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM pooja_requests WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Delete pooja request error: " . $e->getMessage());
            return false;
        }
    }
}
