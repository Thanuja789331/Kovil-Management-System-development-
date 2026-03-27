<?php
require_once __DIR__ . "/../../config/database.php";

class Booking {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Create a new booking
     */
    public function create($schedule_id, $user_id) {
        try {
            // Check if already booked
            $checkStmt = $this->conn->prepare("SELECT id FROM bookings WHERE schedule_id = ? AND user_id = ? AND status = 'confirmed'");
            $checkStmt->bind_param("ii", $schedule_id, $user_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $checkStmt->close();
                return ["success" => false, "message" => "You have already booked this pooja"];
            }
            $checkStmt->close();
            
            $stmt = $this->conn->prepare("INSERT INTO bookings (schedule_id, user_id, status, created_at) VALUES (?, ?, 'confirmed', NOW())");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("ii", $schedule_id, $user_id);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $success ? 
                ["success" => true, "message" => "Booking confirmed", "booking_id" => $insertId] :
                ["success" => false, "message" => "Booking failed. Please try again."];
        } catch (Exception $e) {
            error_log("Booking error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred during booking"];
        }
    }

    /**
     * Get booking by ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT b.*, p.pooja_name, p.pooja_date, p.time_slot, u.name as user_name
            FROM bookings b
            JOIN pooja_schedule p ON b.schedule_id = p.id
            JOIN users u ON b.user_id = u.id
            WHERE b.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();
        $stmt->close();
        return $booking;
    }

    /**
     * Get all bookings for a user
     */
    public function getByUser($user_id) {
        $stmt = $this->conn->prepare("
            SELECT b.*, p.pooja_name, p.pooja_date, p.time_slot
            FROM bookings b
            JOIN pooja_schedule p ON b.schedule_id = p.id
            WHERE b.user_id = ? AND b.status = 'confirmed'
            ORDER BY p.pooja_date ASC, p.time_slot ASC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Cancel a booking
     */
    public function cancel($id) {
        try {
            $stmt = $this->conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            
            return $success;
        } catch (Exception $e) {
            error_log("Cancel booking error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get booking statistics
     */
    public function getTotalBookings() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'confirmed'");
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}