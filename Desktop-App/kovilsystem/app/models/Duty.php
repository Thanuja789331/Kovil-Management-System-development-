<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/User.php";

class Duty {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Assign a priest to a pooja schedule with SMS notification
     */
    public function assign($priest_id, $schedule_id) {
        try {
            // Check if already assigned
            $checkStmt = $this->conn->prepare("SELECT id FROM priest_duties WHERE priest_id = ? AND schedule_id = ?");
            $checkStmt->bind_param("ii", $priest_id, $schedule_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $checkStmt->close();
                return ["success" => false, "message" => "Priest is already assigned to this schedule"];
            }
            $checkStmt->close();
            
            $stmt = $this->conn->prepare("INSERT INTO priest_duties (priest_id, schedule_id, assigned_date, status) VALUES (?, ?, CURDATE(), 'assigned')");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ii", $priest_id, $schedule_id);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            // Send SMS notification to priest
            if ($success) {
                $this->notifyPriest($priest_id, $schedule_id);
            }
            
            return $success ? 
                ["success" => true, "message" => "Duty assigned successfully"] :
                ["success" => false, "message" => "Failed to assign duty"];
        } catch (Exception $e) {
            error_log("Assign duty error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred while assigning duty"];
        }
    }

    /**
     * Notify priest about duty assignment via SMS
     */
    private function notifyPriest($priest_id, $schedule_id) {
        try {
            // Get priest details
            $userModel = new User();
            $priest = $userModel->getById($priest_id);
            
            // Get schedule details
            $stmt = $this->conn->prepare("SELECT pooja_name, pooja_date, time_slot FROM pooja_schedule WHERE id = ?");
            $stmt->bind_param("i", $schedule_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $schedule = $result->fetch_assoc();
            $stmt->close();
            
            if ($priest && $priest['phone'] && $schedule) {
                sendDutyAssignmentSMS(
                    $priest['phone'],
                    $priest['name'],
                    $schedule['pooja_name'],
                    $schedule['pooja_date'],
                    $schedule['time_slot']
                );
            }
        } catch (Exception $e) {
            error_log("Notify priest error: " . $e->getMessage());
        }
    }

    /**
     * Get priest availability - shows which priests are free on a given date
     */
    public function getPriestAvailability($date) {
        $stmt = $this->conn->prepare("
            SELECT u.id, u.name, u.email, u.phone,
                   COUNT(pd.id) as assigned_count
            FROM users u
            LEFT JOIN priest_duties pd ON u.id = pd.priest_id 
                AND pd.assigned_date = ? 
                AND pd.status != 'cancelled'
            WHERE u.role = 'priest' AND u.approval_status = 'approved'
            GROUP BY u.id, u.name, u.email, u.phone
            ORDER BY assigned_count ASC, u.name ASC
        ");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Check if priest is available on a specific date
     */
    public function isPriestAvailable($priest_id, $date) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as count 
            FROM priest_duties 
            WHERE priest_id = ? 
            AND assigned_date = ? 
            AND status != 'cancelled'
        ");
        $stmt->bind_param("is", $priest_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        // Priest is available if they have less than 3 assignments that day
        return $row['count'] < 3;
    }

    /**
     * Get all duties with detailed information
     */
    public function getAllDetailed() {
        $result = $this->conn->query("
            SELECT pd.*, 
                   u.name as priest_name, u.phone as priest_phone,
                   ps.pooja_name, ps.pooja_date, ps.time_slot,
                   pd.notification_sent
            FROM priest_duties pd
            JOIN users u ON pd.priest_id = u.id
            JOIN pooja_schedule ps ON pd.schedule_id = ps.id
            ORDER BY ps.pooja_date ASC, ps.time_slot ASC
        ");
        return $result;
    }

    /**
     * Get all duties assigned to a priest
     */
    public function getByPriest($priest_id) {
        $stmt = $this->conn->prepare("
            SELECT pd.*, ps.pooja_name, ps.pooja_date, ps.time_slot, ps.description
            FROM priest_duties pd
            JOIN pooja_schedule ps ON pd.schedule_id = ps.id
            WHERE pd.priest_id = ?
            ORDER BY ps.pooja_date ASC, ps.time_slot ASC
        ");
        $stmt->bind_param("i", $priest_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Get all duties
     */
    public function getAll() {
        $result = $this->conn->query("
            SELECT pd.*, u.name as priest_name, ps.pooja_name, ps.pooja_date, ps.time_slot
            FROM priest_duties pd
            JOIN users u ON pd.priest_id = u.id
            JOIN pooja_schedule ps ON pd.schedule_id = ps.id
            ORDER BY ps.pooja_date ASC, ps.time_slot ASC
        ");
        return $result;
    }

    /**
     * Update duty status
     */
    public function updateStatus($duty_id, $status) {
        try {
            $stmt = $this->conn->prepare("UPDATE priest_duties SET status = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("si", $status, $duty_id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Update duty status error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove duty assignment
     */
    public function remove($duty_id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM priest_duties WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $duty_id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Remove duty error: " . $e->getMessage());
            return false;
        }
    }
}