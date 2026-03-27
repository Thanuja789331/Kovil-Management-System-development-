<?php
require_once __DIR__ . "/../../config/database.php";

class Schedule {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Get all pooja schedules
     */
    public function getAll() {
        $result = $this->conn->query("
            SELECT * FROM pooja_schedule 
            WHERE pooja_date >= CURDATE()
            ORDER BY pooja_date ASC, time_slot ASC
        ");
        return $result;
    }

    /**
     * Get schedule by ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM pooja_schedule WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result->fetch_assoc();
        $stmt->close();
        return $schedule;
    }

    /**
     * Get available schedules only
     */
    public function getAvailable() {
        $result = $this->conn->query("
            SELECT * FROM pooja_schedule 
            WHERE status = 'available' AND pooja_date >= CURDATE()
            ORDER BY pooja_date ASC, time_slot ASC
        ");
        return $result;
    }

    /**
     * Mark a schedule as booked
     */
    public function markBooked($id) {
        try {
            $stmt = $this->conn->prepare("UPDATE pooja_schedule SET status = 'booked' WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Error marking schedule as booked: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if schedule is available for booking
     */
    public function isAvailable($id) {
        $stmt = $this->conn->prepare("SELECT status FROM pooja_schedule WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result->fetch_assoc();
        $stmt->close();
        
        return $schedule && $schedule['status'] === 'available';
    }

    /**
     * Create new pooja schedule (for admin)
     */
    public function create($pooja_name, $pooja_date, $time_slot, $description = '') {
        try {
            $stmt = $this->conn->prepare("INSERT INTO pooja_schedule (pooja_name, pooja_date, time_slot, description) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ssss", $pooja_name, $pooja_date, $time_slot, $description);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $success ? $insertId : false;
        } catch (Exception $e) {
            error_log("Error creating schedule: " . $e->getMessage());
            return false;
        }
    }
}