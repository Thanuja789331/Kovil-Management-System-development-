<?php
require_once __DIR__ . "/../../config/database.php";

class Festival {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Create a new festival
     */
    public function create($name, $date, $description = '', $image_url = null) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO festivals (name, date, description, image_url) VALUES (?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ssss", $name, $date, $description, $image_url);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $success ? 
                ["success" => true, "message" => "Festival created successfully", "id" => $insertId] :
                ["success" => false, "message" => "Failed to create festival"];
        } catch (Exception $e) {
            error_log("Festival error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred while creating festival"];
        }
    }

    /**
     * Get all festivals
     */
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM festivals ORDER BY date ASC");
        return $result;
    }

    /**
     * Get upcoming festivals only
     */
    public function getUpcoming() {
        $result = $this->conn->query("SELECT * FROM festivals WHERE date >= CURDATE() ORDER BY date ASC");
        return $result;
    }

    /**
     * Get festival by ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM festivals WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $festival = $result->fetch_assoc();
        $stmt->close();
        return $festival;
    }

    /**
     * Update festival
     */
    public function update($id, $name, $date, $description = '') {
        try {
            $stmt = $this->conn->prepare("UPDATE festivals SET name = ?, date = ?, description = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("sssi", $name, $date, $description, $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Update festival error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a festival
     */
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM festivals WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Delete festival error: " . $e->getMessage());
            return false;
        }
    }
}
