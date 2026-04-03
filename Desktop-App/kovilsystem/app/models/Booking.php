<?php
require_once __DIR__ . "/../../config/database.php";

class Booking {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Create a new booking with phone number and special requests
     */
    public function create($schedule_id, $user_id, $devotee_phone, $special_requests = '') {
        try {
            // Validate inputs
            if ($schedule_id <= 0 || $user_id <= 0) {
                return ["success" => false, "message" => "Invalid schedule or user ID"];
            }
            
            // Validate phone number format
            if (!preg_match('/^[0-9]{10}$/', $devotee_phone)) {
                return ["success" => false, "message" => "Invalid phone number. Please enter 10 digits."];
            }
            
            // Check if schedule exists and is available
            $scheduleCheck = $this->conn->prepare("SELECT id, status FROM pooja_schedule WHERE id = ?");
            $scheduleCheck->bind_param("i", $schedule_id);
            $scheduleCheck->execute();
            $scheduleResult = $scheduleCheck->get_result();
            
            if ($scheduleResult->num_rows === 0) {
                $scheduleCheck->close();
                error_log("Schedule ID {$schedule_id} does not exist");
                return ["success" => false, "message" => "Selected pooja does not exist"];
            }
            
            $schedule = $scheduleResult->fetch_assoc();
            if ($schedule['status'] !== 'available') {
                $scheduleCheck->close();
                error_log("Schedule ID {$schedule_id} is not available (status: {$schedule['status']})");
                return ["success" => false, "message" => "This pooja is no longer available for booking"];
            }
            $scheduleCheck->close();
            
            // Check if already booked by this user
            $checkStmt = $this->conn->prepare("SELECT id FROM bookings WHERE schedule_id = ? AND user_id = ? AND status = 'confirmed'");
            $checkStmt->bind_param("ii", $schedule_id, $user_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $checkStmt->close();
                return ["success" => false, "message" => "You have already booked this pooja"];
            }
            $checkStmt->close();
            
            // Generate unique booking reference
            $bookingRef = 'BKG' . strtoupper(uniqid());
            
            $stmt = $this->conn->prepare("INSERT INTO bookings (booking_reference, schedule_id, user_id, phone, special_requests, status, created_at) VALUES (?, ?, ?, ?, ?, 'confirmed', NOW())");
            
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("siiss", $bookingRef, $schedule_id, $user_id, $devotee_phone, $special_requests);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $error = $stmt->error;
            $stmt->close();
            
            if (!$success) {
                error_log("Booking insert failed: " . $error);
                return ["success" => false, "message" => "Booking failed. Please try again."];
            }
            
            // Send SMS notification if booking successful
            if ($success) {
                $this->sendBookingSMS($insertId);
            }
            
            error_log("Booking created successfully - ID: {$insertId}, Reference: {$bookingRef}");
            return ["success" => true, "message" => "Booking confirmed successfully", "booking_id" => $insertId, "booking_reference" => $bookingRef];
        } catch (Exception $e) {
            error_log("Booking exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return ["success" => false, "message" => "An error occurred during booking: " . $e->getMessage()];
        }
    }

    /**
     * Send booking confirmation SMS
     */
    private function sendBookingSMS($bookingId) {
        try {
            $booking = $this->getById($bookingId);
            if ($booking && $booking['phone']) {
                // Format enhanced confirmation message
                $message = "🛕 POOJA BOOKING CONFIRMATION 🛕\n\n";
                $message .= "Ref: {$booking['booking_reference']}\n";
                $message .= "Pooja: {$booking['pooja_name']}\n";
                $message .= "Date: " . date("M j, Y", strtotime($booking['pooja_date'])) . "\n";
                $message .= "Time: {$booking['time_slot']}\n";
                $message .= "Devotee: {$booking['user_name']}\n";
                $message .= "Phone: {$booking['phone']}\n\n";
                
                if (!empty($booking['special_requests'])) {
                    $message .= "Special: " . substr($booking['special_requests'], 0, 50) . "...\n\n";
                }
                
                $message .= "Status: ✓ Confirmed\n\n";
                $message .= "Please arrive 15 mins early.\n";
                $message .= "Show this msg at temple.\n\n";
                $message .= "Thank you! 🙏";
                
                require_once __DIR__ . '/../../config/sms_helper.php';
                $result = sendSMS($booking['phone'], $message, 'booking_confirmation');
                
                error_log("Auto-SMS sent for Booking #{$bookingId} to {$booking['phone']} - Success: " . ($result['success'] ? 'Yes' : 'No'));
                
                // Mark SMS as sent in database
                $stmt = $this->conn->prepare("UPDATE bookings SET sms_sent = 1 WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $bookingId);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        } catch (Exception $e) {
            error_log("Send booking SMS error: " . $e->getMessage());
        }
    }

    /**
     * Check if a time slot is available for a specific date
     */
    public function isTimeSlotAvailable($pooja_date, $time_slot) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as count 
            FROM pooja_schedule 
            WHERE pooja_date = ? AND time_slot = ? AND status = 'available'
        ");
        $stmt->bind_param("ss", $pooja_date, $time_slot);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'] > 0;
    }

    /**
     * Get available poojas for a specific date
     */
    public function getAvailablePoojasForDate($date) {
        $stmt = $this->conn->prepare("
            SELECT id, pooja_name, time_slot, description
            FROM pooja_schedule
            WHERE pooja_date = ? AND status = 'available'
            ORDER BY time_slot ASC
        ");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Validate booking data
     */
    public function validateBooking($data) {
        $errors = [];
        
        if (empty($data['schedule_id'])) {
            $errors[] = "Please select a pooja";
        }
        
        if (empty($data['phone'])) {
            $errors[] = "Phone number is required";
        } elseif (!preg_match('/^[0-9]{10}$|^\+[0-9]{1,3}[0-9]{10}$/', $data['phone'])) {
            $errors[] = "Please enter a valid phone number (10 digits or international format)";
        }
        
        if (!empty($data['schedule_id'])) {
            $schedule = $this->getScheduleById($data['schedule_id']);
            if (!$schedule) {
                $errors[] = "Invalid pooja selected";
            } elseif ($schedule['status'] !== 'available') {
                $errors[] = "This pooja is no longer available";
            }
        }
        
        return $errors;
    }

    /**
     * Get schedule details by ID
     */
    private function getScheduleById($id) {
        $stmt = $this->conn->prepare("SELECT id, status FROM pooja_schedule WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result->fetch_assoc();
        $stmt->close();
        return $schedule;
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