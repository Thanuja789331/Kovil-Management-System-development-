<?php
require_once __DIR__ . "/../../config/database.php";

class Announcement {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Create a new announcement
     */
    public function create($title, $message, $date, $created_by = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO announcements (title, message, date, created_by) VALUES (?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("sssi", $title, $message, $date, $created_by);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $success ? 
                ["success" => true, "message" => "Announcement created successfully", "id" => $insertId] :
                ["success" => false, "message" => "Failed to create announcement"];
        } catch (Exception $e) {
            error_log("Announcement error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred while creating announcement"];
        }
    }

    /**
     * Get all announcements
     */
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM announcements ORDER BY date DESC, created_at DESC");
        return $result;
    }

    /**
     * Get announcement by ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcement = $result->fetch_assoc();
        $stmt->close();
        return $announcement;
    }

    /**
     * Delete an announcement
     */
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM announcements WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Delete announcement error: " . $e->getMessage());
            return false;
        }
    }
}
