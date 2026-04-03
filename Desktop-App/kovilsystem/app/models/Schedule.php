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

    /**
     * Get all poojas with booking information
     */
    public function getAllWithBookings() {
        $result = $this->conn->query("
            SELECT 
                ps.id,
                ps.pooja_name,
                ps.pooja_date,
                ps.time_slot,
                ps.description,
                ps.status,
                ps.created_at,
                u.name as booked_by_name
            FROM pooja_schedule ps
            LEFT JOIN bookings b ON ps.id = b.schedule_id
            LEFT JOIN users u ON b.user_id = u.id
            ORDER BY ps.pooja_date DESC, ps.time_slot ASC
        ");
        return $result;
    }

    /**
     * Get all poojas with booking information and pagination
     */
    public function getAllWithBookingsPaginated($perPage, $offset) {
        $result = $this->conn->query("
            SELECT 
                ps.id,
                ps.pooja_name,
                ps.pooja_date,
                ps.time_slot,
                ps.description,
                ps.status,
                ps.created_at,
                u.name as booked_by_name
            FROM pooja_schedule ps
            LEFT JOIN bookings b ON ps.id = b.schedule_id
            LEFT JOIN users u ON b.user_id = u.id
            ORDER BY ps.pooja_date DESC, ps.time_slot ASC
            LIMIT $perPage OFFSET $offset
        ");
        return $result;
    }

    /**
     * Get total count of poojas
     */
    public function getTotalCount() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM pooja_schedule");
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    /**
     * Get most popular poojas (most booked)
     */
    public function getPopularPoojas() {
        $result = $this->conn->query("
            SELECT 
                ps.pooja_name,
                COUNT(b.id) as booking_count
            FROM pooja_schedule ps
            LEFT JOIN bookings b ON ps.id = b.schedule_id
            GROUP BY ps.pooja_name
            HAVING booking_count > 0
            ORDER BY booking_count DESC
            LIMIT 5
        ");
        return $result;
    }

    /**
     * Get busiest time slots
     */
    public function getBusyTimeSlots() {
        $result = $this->conn->query("
            SELECT 
                ps.time_slot,
                COUNT(b.id) as booking_count,
                TIME_FORMAT(ps.time_slot, '%l:%i %p') as formatted_time
            FROM pooja_schedule ps
            LEFT JOIN bookings b ON ps.id = b.schedule_id
            GROUP BY ps.time_slot
            HAVING booking_count > 0
            ORDER BY booking_count DESC
            LIMIT 5
        ");
        return $result;
    }

    /**
     * Get monthly trends (poojas per month)
     */
    public function getMonthlyTrends() {
        $result = $this->conn->query("
            SELECT 
                DATE_FORMAT(pooja_date, '%Y-%m') as month,
                DATE_FORMAT(pooja_date, '%M %Y') as formatted_month,
                COUNT(*) as total_poojas,
                SUM(CASE WHEN status = 'booked' THEN 1 ELSE 0 END) as booked_count,
                SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_count
            FROM pooja_schedule
            GROUP BY DATE_FORMAT(pooja_date, '%Y-%m')
            ORDER BY month DESC
            LIMIT 6
        ");
        return $result;
    }

    /**
     * Update existing pooja schedule
     */
    public function update($id, $pooja_name, $pooja_date, $time_slot, $description = '') {
        try {
            $stmt = $this->conn->prepare("UPDATE pooja_schedule SET pooja_name = ?, pooja_date = ?, time_slot = ?, description = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ssssi", $pooja_name, $pooja_date, $time_slot, $description, $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Error updating schedule: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete pooja schedule
     */
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM pooja_schedule WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Error deleting schedule: " . $e->getMessage());
            return false;
        }
    }
}