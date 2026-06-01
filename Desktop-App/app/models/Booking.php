<?php
require_once __DIR__ . "/../../config/database.php";

class Booking {
    private $conn;
    private static $emailColumnsChecked = false;
    private static $integrityChecked = false;
    private static $bookingPhoneColumn = null;
    private const MAX_SPECIAL_REQUESTS_LENGTH = 1000;

    public function __construct() {
        $this->conn = Database::connect();
        $this->ensureIntegrityConstraints();
    }

    /**
     * Create a new booking with phone number and special requests
     */
    public function create($schedule_id, $user_id, $devotee_phone, $special_requests = '', $notificationPreference = 'both') {
        try {
            $this->ensureEmailReminderColumns();
            $schedule_id = (int) $schedule_id;
            $user_id = (int) $user_id;
            $devotee_phone = $this->normalizePhone($devotee_phone);
            $special_requests = $this->sanitizeSpecialRequests($special_requests);
            $notificationPreference = strtolower(trim((string) $notificationPreference));

            $validationErrors = $this->validateBooking([
                'schedule_id' => $schedule_id,
                'user_id' => $user_id,
                'phone' => $devotee_phone,
                'special_requests' => $special_requests,
                'notification_preference' => $notificationPreference,
            ]);
            if (!empty($validationErrors)) {
                return ["success" => false, "message" => $validationErrors[0]];
            }
            
            // Generate unique booking reference
            $bookingRef = 'BKG' . strtoupper(uniqid());

            $this->conn->begin_transaction();
            $scheduleCheck = $this->conn->prepare("SELECT id, status FROM pooja_schedule WHERE id = ? FOR UPDATE");
            $scheduleCheck->bind_param("i", $schedule_id);
            $scheduleCheck->execute();
            $scheduleResult = $scheduleCheck->get_result();

            if ($scheduleResult->num_rows === 0) {
                $scheduleCheck->close();
                $this->conn->rollback();
                return ["success" => false, "message" => "Selected pooja does not exist"];
            }

            $schedule = $scheduleResult->fetch_assoc();
            $scheduleCheck->close();
            if ($schedule['status'] !== 'available') {
                $this->conn->rollback();
                return ["success" => false, "message" => "This pooja is no longer available for booking"];
            }

            $checkStmt = $this->conn->prepare("SELECT id, user_id FROM bookings WHERE schedule_id = ? AND status = 'confirmed' LIMIT 1");
            $checkStmt->bind_param("i", $schedule_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            if ($result->num_rows > 0) {
                $existing = $result->fetch_assoc();
                $checkStmt->close();
                $this->conn->rollback();
                if ((int) ($existing['user_id'] ?? 0) === (int) $user_id) {
                    return ["success" => false, "message" => "You have already booked this pooja"];
                }
                return ["success" => false, "message" => "This pooja is already booked by another devotee"];
            }
            $checkStmt->close();

            $hasPreferenceColumn = $this->columnExists('bookings', 'notification_preference');
            $phoneColumn = $this->getBookingPhoneColumn();
            if ($hasPreferenceColumn) {
                $stmt = $this->conn->prepare("INSERT INTO bookings (booking_reference, schedule_id, user_id, {$phoneColumn}, special_requests, notification_preference, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'confirmed', NOW())");
            } else {
                $stmt = $this->conn->prepare("INSERT INTO bookings (booking_reference, schedule_id, user_id, {$phoneColumn}, special_requests, status, created_at) VALUES (?, ?, ?, ?, ?, 'confirmed', NOW())");
            }
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            if ($hasPreferenceColumn) {
                $stmt->bind_param("siisss", $bookingRef, $schedule_id, $user_id, $devotee_phone, $special_requests, $notificationPreference);
            } else {
                $stmt->bind_param("siiss", $bookingRef, $schedule_id, $user_id, $devotee_phone, $special_requests);
            }
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $error = $stmt->error;
            $stmt->close();
            if (!$success) {
                $this->conn->rollback();
                error_log("Booking insert failed: " . $error);
                return ["success" => false, "message" => "Booking failed. Please try again."];
            }

            $statusStmt = $this->conn->prepare("UPDATE pooja_schedule SET status = 'booked' WHERE id = ? AND status = 'available'");
            $statusStmt->bind_param("i", $schedule_id);
            $statusStmt->execute();
            if ($statusStmt->affected_rows === 0) {
                $statusStmt->close();
                $this->conn->rollback();
                return ["success" => false, "message" => "This pooja is no longer available for booking"];
            }
            $statusStmt->close();
            $this->conn->commit();
            
            // Send confirmation on selected channels if booking successful
            if ($success) {
                $this->sendBookingNotification($insertId, $notificationPreference);
            }
            
            error_log("Booking created successfully - ID: {$insertId}, Reference: {$bookingRef}");
            return ["success" => true, "message" => "Booking confirmed successfully", "booking_id" => $insertId, "booking_reference" => $bookingRef];
        } catch (Exception $e) {
            try {
                $this->conn->rollback();
            } catch (Exception $rollbackException) {
                // Ignore rollback errors and keep original exception context.
            }
            error_log("Booking exception: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return ["success" => false, "message" => "An error occurred during booking. Please try again."];
        }
    }

    /**
     * Send pending booking reminder emails (10-day and 3-day).
     * Intended to be called by lightweight app traffic as a simple scheduler.
     */
    public function processUpcomingEmailReminders() {
        try {
            $this->ensureEmailReminderColumns();
            $sql = "
                SELECT
                    b.id,
                    b.booking_reference,
                    b.special_requests,
                    b.{$this->getBookingPhoneColumn()} AS phone,
                    b.notification_preference,
                    b.reminder_10_day_email_sent,
                    b.reminder_3_day_email_sent,
                    b.reminder_24_hour_email_sent,
                    p.pooja_name,
                    p.pooja_date,
                    p.time_slot,
                    u.name AS user_name,
                    u.email
                FROM bookings b
                JOIN pooja_schedule p ON b.schedule_id = p.id
                JOIN users u ON b.user_id = u.id
                WHERE b.status = 'confirmed'
                  AND b.notification_preference IN ('email', 'both')
                  AND p.pooja_date >= CURDATE()
                  AND DATEDIFF(p.pooja_date, CURDATE()) IN (1, 3, 10)
            ";
            $result = $this->conn->query($sql);
            if (!$result) {
                return;
            }

            while ($booking = $result->fetch_assoc()) {
                $daysLeft = (int) ((strtotime($booking['pooja_date']) - strtotime(date('Y-m-d'))) / 86400);
                if ($daysLeft === 10 && (int) $booking['reminder_10_day_email_sent'] === 0) {
                    if ($this->sendReminderEmail($booking, 10)) {
                        $this->markReminderAsSent((int) $booking['id'], 10);
                    }
                } elseif ($daysLeft === 3 && (int) $booking['reminder_3_day_email_sent'] === 0) {
                    if ($this->sendReminderEmail($booking, 3)) {
                        $this->markReminderAsSent((int) $booking['id'], 3);
                    }
                } elseif ($daysLeft === 1 && (int) $booking['reminder_24_hour_email_sent'] === 0) {
                    if ($this->sendReminderEmail($booking, 1)) {
                        $this->markReminderAsSent((int) $booking['id'], 1);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Email reminder processing error: " . $e->getMessage());
        }
    }

    /**
     * Send booking confirmation SMS
     */
    private function sendBookingSMS($bookingId) {
        try {
            $booking = $this->getById($bookingId);
            if ($booking && $booking['phone']) {
                $message = $this->buildConfirmationMessage($booking);
                
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
                return !empty($result['success']);
            }
        } catch (Exception $e) {
            error_log("Send booking SMS error: " . $e->getMessage());
        }
        return false;
    }

    /**
     * Send booking confirmation email with bring-items checklist.
     */
    public function sendBookingConfirmationEmail($bookingId) {
        try {
            $booking = $this->getById($bookingId);
            if (!$booking || empty($booking['email']) || !function_exists('sendEmailNotification')) {
                return false;
            }

            $subject = "Pooja Booking Receipt - {$booking['pooja_name']}";
            $body = $this->buildBookingEmailBody($booking, 'confirmation');
            $sent = sendEmailNotification($booking['email'], $subject, $body, 'booking_confirmation_email');

            if (!empty($sent['success'])) {
                $stmt = $this->conn->prepare("UPDATE bookings SET confirmation_email_sent = 1 WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $bookingId);
                    $stmt->execute();
                    $stmt->close();
                }
                return true;
            }
        } catch (Exception $e) {
            error_log("Booking confirmation email error: " . $e->getMessage());
        }
        return false;
    }

    public function sendBookingNotification($bookingId, $channel = 'both') {
        $channel = strtolower(trim((string) $channel));
        if (!in_array($channel, ['sms', 'email', 'both'], true)) {
            return ['success' => false, 'message' => 'Invalid channel selected'];
        }

        $smsOk = false;
        $emailOk = false;

        if ($channel === 'sms' || $channel === 'both') {
            $smsOk = $this->sendBookingSMS($bookingId);
        }
        if ($channel === 'email' || $channel === 'both') {
            $emailOk = $this->sendBookingConfirmationEmail($bookingId);
        }

        if ($channel === 'sms') {
            return ['success' => $smsOk, 'message' => $smsOk ? 'Confirmation sent by SMS.' : 'SMS sending failed.'];
        }
        if ($channel === 'email') {
            return ['success' => $emailOk, 'message' => $emailOk ? 'Confirmation receipt sent by email.' : 'Email sending failed.'];
        }
        if ($smsOk && $emailOk) {
            return ['success' => true, 'message' => 'Confirmation sent by SMS and email.'];
        }
        if ($smsOk || $emailOk) {
            return ['success' => true, 'message' => 'Confirmation sent to selected channels (partial success).'];
        }
        return ['success' => false, 'message' => 'Failed to send confirmation by SMS and email.'];
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
        $scheduleId = (int) ($data['schedule_id'] ?? 0);
        $userId = (int) ($data['user_id'] ?? 0);
        $phone = $this->normalizePhone($data['phone'] ?? '');
        $specialRequests = (string) ($data['special_requests'] ?? '');
        $notificationPreference = strtolower(trim((string) ($data['notification_preference'] ?? 'both')));
        $phoneRequired = in_array($notificationPreference, ['sms', 'both'], true);

        if ($scheduleId <= 0) {
            $errors[] = "Please select a pooja";
        }

        if ($userId <= 0) {
            $errors[] = "A valid devotee account is required to complete this booking";
        }

        if (!in_array($notificationPreference, ['sms', 'email', 'both'], true)) {
            $errors[] = "Invalid notification preference selected.";
        }

        if ($phoneRequired && $phone === '') {
            $errors[] = "Phone number is required";
        } elseif ($phoneRequired && !$this->isValidPhone($phone)) {
            $errors[] = "Please enter a valid phone number (10 digits or international format)";
        } elseif (!$phoneRequired && $phone !== '' && !$this->isValidPhone($phone)) {
            $errors[] = "Please enter a valid phone number (10 digits or international format)";
        }

        if ($specialRequests !== '' && !$this->isValidSpecialRequests($specialRequests)) {
            $errors[] = "Special requests must be " . self::MAX_SPECIAL_REQUESTS_LENGTH . " characters or fewer";
        }

        if ($scheduleId > 0) {
            $schedule = $this->getScheduleById($scheduleId);
            if (!$schedule) {
                $errors[] = "Invalid pooja selected";
            } elseif ($schedule['status'] !== 'available') {
                $errors[] = "This pooja is no longer available";
            }
        }

        return $errors;
    }

    public function isValidPhone($phone) {
        $phone = $this->normalizePhone($phone);
        if ($phone === '') {
            return true;
        }
        return (bool) preg_match('/^[0-9]{10}$|^\+[0-9]{1,3}[0-9]{10}$/', $phone);
    }

    public function isValidSpecialRequests($specialRequests) {
        $specialRequests = trim((string) $specialRequests);
        if ($specialRequests === '') {
            return true;
        }
        if (strlen($specialRequests) > self::MAX_SPECIAL_REQUESTS_LENGTH) {
            return false;
        }
        return (bool) preg_match('/^[\p{L}\p{N}\s.,\-\'!?()\/]+$/u', $specialRequests);
    }

    public function normalizePhone($phone) {
        return preg_replace('/\s+/', '', trim((string) $phone));
    }

    public function sanitizeSpecialRequests($specialRequests) {
        $specialRequests = trim((string) $specialRequests);
        if ($specialRequests === '') {
            return '';
        }
        $specialRequests = preg_replace('/\s+/u', ' ', $specialRequests);
        if (strlen($specialRequests) > self::MAX_SPECIAL_REQUESTS_LENGTH) {
            $specialRequests = mb_substr($specialRequests, 0, self::MAX_SPECIAL_REQUESTS_LENGTH);
        }
        return $specialRequests;
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
            SELECT b.*, p.pooja_name, p.pooja_date, p.time_slot, u.name as user_name, u.email, b.{$this->getBookingPhoneColumn()} AS phone
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
            SELECT b.*, p.pooja_name, p.pooja_date, p.time_slot, p.description AS schedule_description
            FROM bookings b
            JOIN pooja_schedule p ON b.schedule_id = p.id
            WHERE b.user_id = ?
            ORDER BY p.pooja_date DESC, p.time_slot DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Search bookings by devotee name, phone, or booking reference (management only)
     */
    public function searchBookings($query) {
        $q = '%' . $query . '%';
        $phoneCol = $this->getBookingPhoneColumn();
        $stmt = $this->conn->prepare("
            SELECT b.id, b.booking_reference, b.status, b.created_at, b.cancelled_at,
                   b.{$phoneCol} AS devotee_phone,
                   p.pooja_name, p.pooja_date, p.time_slot,
                   u.name AS user_name, u.email AS user_email, u.phone AS user_phone
            FROM bookings b
            JOIN pooja_schedule p ON b.schedule_id = p.id
            JOIN users u ON b.user_id = u.id
            WHERE u.name LIKE ?
               OR u.phone LIKE ?
               OR b.{$phoneCol} LIKE ?
               OR b.booking_reference LIKE ?
            ORDER BY p.pooja_date DESC, b.created_at DESC
        ");
        $stmt->bind_param("ssss", $q, $q, $q, $q);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Cancel a booking
     */
    public function cancel($id, $user_id = null) {
        try {
            $this->conn->begin_transaction();

            // Fetch the booking details to find the schedule_id
            $query = "SELECT schedule_id, user_id FROM bookings WHERE id = ? FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                $this->conn->rollback();
                return false;
            }
            $booking = $result->fetch_assoc();
            $stmt->close();

            // If user_id is passed, verify ownership
            if ($user_id !== null && (int)$booking['user_id'] !== (int)$user_id) {
                $this->conn->rollback();
                return false;
            }

            // Update booking status and record cancellation time
            $updateBooking = $this->conn->prepare("UPDATE bookings SET status = 'cancelled', cancelled_at = NOW() WHERE id = ?");
            $updateBooking->bind_param("i", $id);
            $updateBooking->execute();
            $updateBooking->close();

            // Update pooja schedule status to available
            $updateSchedule = $this->conn->prepare("UPDATE pooja_schedule SET status = 'available' WHERE id = ?");
            $updateSchedule->bind_param("i", $booking['schedule_id']);
            $updateSchedule->execute();
            $updateSchedule->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
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

    public function getTotalBookingsInRange($startDate, $endDate) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as total 
            FROM bookings b
            JOIN pooja_schedule p ON b.schedule_id = p.id
            WHERE b.status = 'confirmed' AND p.pooja_date BETWEEN ? AND ?
        ");
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row['total'];
    }

    /**
     * Build a reusable booking confirmation message.
     */
    public function buildConfirmationMessage($booking) {
        $message = "POOJA BOOKING CONFIRMATION\n\n";
        $message .= "Ref: {$booking['booking_reference']}\n";
        $message .= "Pooja: {$booking['pooja_name']}\n";
        $message .= "Date: " . date("M j, Y", strtotime($booking['pooja_date'])) . "\n";
        $message .= "Time: {$booking['time_slot']}\n";
        $message .= "Booked By: {$booking['user_name']}\n";
        $message .= "Phone: {$booking['phone']}\n\n";
        if (!empty($booking['special_requests'])) {
            $message .= "Special Requests: {$booking['special_requests']}\n\n";
        }
        $items = $this->getBringItemsForPooja($booking['pooja_name']);
        $message .= "Bring Items:\n";
        foreach ($items as $item) {
            $message .= "- {$item}\n";
        }
        $message .= "\n";
        $message .= "Status: Confirmed\n\n";
        $message .= "Please arrive 15 minutes early.\n";
        $message .= "Show this message at the temple.\n\n";
        $message .= "Thank you for your devotion.";
        return $message;
    }

    private function sendReminderEmail($booking, $daysLeft) {
        if (empty($booking['email']) || !function_exists('sendEmailNotification')) {
            return false;
        }

        $subject = "Pooja Reminder - {$daysLeft} day(s) left for {$booking['pooja_name']}";
        $body = $this->buildBookingEmailBody($booking, 'reminder', $daysLeft);
        $sent = sendEmailNotification($booking['email'], $subject, $body, "booking_reminder_{$daysLeft}_day");

        return !empty($sent['success']);
    }

    private function buildBookingEmailBody($booking, $mode = 'confirmation', $daysLeft = null) {
        $dateText = date("F j, Y", strtotime($booking['pooja_date']));
        $items = $this->getBringItemsForPooja($booking['pooja_name']);
        $itemsText = "- " . implode("\n- ", $items);

        $intro = "Your pooja booking is confirmed.";
        if ($mode === 'reminder' && $daysLeft !== null) {
            $intro = "This is a reminder that your pooja is in {$daysLeft} day(s).";
        }

        $body = "Vanakkam {$booking['user_name']},\n\n";
        $body .= "{$intro}\n\n";
        $body .= "Booking Receipt\n";
        $body .= "Reference: {$booking['booking_reference']}\n";
        $body .= "Pooja: {$booking['pooja_name']}\n";
        $body .= "Date: {$dateText}\n";
        $body .= "Time: {$booking['time_slot']}\n";
        $body .= "Contact Phone: {$booking['phone']}\n";
        $body .= "Status: Confirmed\n\n";

        if (!empty($booking['special_requests'])) {
            $body .= "Special Requests: {$booking['special_requests']}\n\n";
        }

        $body .= "Please bring the following items:\n{$itemsText}\n\n";
        $body .= "Kindly arrive at least 15 minutes early.\n";
        $body .= "May divine blessings be with you.";

        return $body;
    }

    private function getBringItemsForPooja($poojaName) {
        $name = strtolower((string) $poojaName);
        $defaultItems = [
            "Coconut (1 or 2)",
            "Bananas or seasonal fruits",
            "Flowers and garland",
            "Betel leaves and arecanut",
            "Camphor and incense sticks",
            "Ghee or oil for lamp"
        ];

        $poojaItemsMap = [
            'ganesh' => array_merge(["Modakam or kozhukattai", "Arugampul garland"], $defaultItems),
            'vinayagar' => array_merge(["Modakam or kozhukattai", "Arugampul garland"], $defaultItems),
            'shiva' => array_merge(["Vilva leaves", "Milk, curd, honey for abhishekam"], $defaultItems),
            'rudra' => array_merge(["Vilva leaves", "Milk, curd, honey for abhishekam"], $defaultItems),
            'lakshmi' => array_merge(["Lotus or red flowers", "Turmeric and kumkum"], $defaultItems),
            'kubera' => array_merge(["Coins or symbolic offering", "Turmeric and kumkum"], $defaultItems),
            'satyanarayana' => array_merge(["Panchamirtham ingredients", "Aval, banana, jaggery"], $defaultItems),
            'murugan' => array_merge(["Panneer flowers", "Paal (milk) for abhishekam"], $defaultItems),
            'amman' => array_merge(["Lemon garland", "Turmeric and kumkum"], $defaultItems),
            'vishnu' => array_merge(["Tulasi leaves", "Butter or sweet pongal"], $defaultItems)
        ];

        foreach ($poojaItemsMap as $keyword => $items) {
            if (strpos($name, $keyword) !== false) {
                return array_values(array_unique($items));
            }
        }

        return $defaultItems;
    }

    /**
     * Expose bring-items list for receipt views/PDF.
     */
    public function getBringItemsForBooking($booking) {
        $poojaName = is_array($booking) ? ($booking['pooja_name'] ?? '') : (string) $booking;
        return $this->getBringItemsForPooja($poojaName);
    }

    /**
     * Admin-only: adjust booking owner/status for a schedule.
     */
    public function adminManageScheduleBooking($scheduleId, $targetStatus, $newUserId = null) {
        $targetStatus = strtolower(trim((string) $targetStatus));
        if (!in_array($targetStatus, ['booked', 'available'], true)) {
            return ['success' => false, 'message' => 'Invalid target status'];
        }
        if ($targetStatus === 'booked' && (int) $newUserId <= 0) {
            return ['success' => false, 'message' => 'Please select a devotee for booked status'];
        }

        try {
            $this->conn->begin_transaction();

            $scheduleStmt = $this->conn->prepare("SELECT id FROM pooja_schedule WHERE id = ? FOR UPDATE");
            $scheduleStmt->bind_param("i", $scheduleId);
            $scheduleStmt->execute();
            $scheduleResult = $scheduleStmt->get_result();
            if ($scheduleResult->num_rows === 0) {
                $scheduleStmt->close();
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Schedule not found'];
            }
            $scheduleStmt->close();

            $currentBookingStmt = $this->conn->prepare("SELECT id FROM bookings WHERE schedule_id = ? AND status = 'confirmed' LIMIT 1 FOR UPDATE");
            $currentBookingStmt->bind_param("i", $scheduleId);
            $currentBookingStmt->execute();
            $currentBookingResult = $currentBookingStmt->get_result();
            $currentBooking = $currentBookingResult->fetch_assoc();
            $currentBookingStmt->close();

            if ($targetStatus === 'available') {
                if (!empty($currentBooking['id'])) {
                    $cancelStmt = $this->conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
                    $cancelStmt->bind_param("i", $currentBooking['id']);
                    $cancelStmt->execute();
                    $cancelStmt->close();
                }
                $statusStmt = $this->conn->prepare("UPDATE pooja_schedule SET status = 'available' WHERE id = ?");
                $statusStmt->bind_param("i", $scheduleId);
                $statusStmt->execute();
                $statusStmt->close();

                $this->conn->commit();
                return ['success' => true, 'message' => 'Pooja marked as available'];
            }

            $userStmt = $this->conn->prepare("SELECT id, phone FROM users WHERE id = ? AND role = 'devotee' AND approval_status = 'approved' LIMIT 1");
            $userStmt->bind_param("i", $newUserId);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            $user = $userResult->fetch_assoc();
            $userStmt->close();
            if (!$user) {
                $this->conn->rollback();
                return ['success' => false, 'message' => 'Selected devotee is invalid or not approved'];
            }

            $phoneColumn = $this->getBookingPhoneColumn();
            $userPhone = $user['phone'] ?? '';

            if (!empty($currentBooking['id'])) {
                $reassignStmt = $this->conn->prepare("UPDATE bookings SET user_id = ?, {$phoneColumn} = ?, status = 'confirmed' WHERE id = ?");
                $reassignStmt->bind_param("isi", $newUserId, $userPhone, $currentBooking['id']);
                $reassignStmt->execute();
                $reassignStmt->close();
            } else {
                $bookingRef = 'BKG' . strtoupper(uniqid());
                $hasPreferenceColumn = $this->columnExists('bookings', 'notification_preference');
                if ($hasPreferenceColumn) {
                    $createStmt = $this->conn->prepare("INSERT INTO bookings (booking_reference, schedule_id, user_id, {$phoneColumn}, special_requests, notification_preference, status, created_at) VALUES (?, ?, ?, ?, '', 'both', 'confirmed', NOW())");
                    $createStmt->bind_param("siis", $bookingRef, $scheduleId, $newUserId, $userPhone);
                } else {
                    $createStmt = $this->conn->prepare("INSERT INTO bookings (booking_reference, schedule_id, user_id, {$phoneColumn}, special_requests, status, created_at) VALUES (?, ?, ?, ?, '', 'confirmed', NOW())");
                    $createStmt->bind_param("siis", $bookingRef, $scheduleId, $newUserId, $userPhone);
                }
                $createStmt->execute();
                $createStmt->close();
            }

            $statusStmt = $this->conn->prepare("UPDATE pooja_schedule SET status = 'booked' WHERE id = ?");
            $statusStmt->bind_param("i", $scheduleId);
            $statusStmt->execute();
            $statusStmt->close();

            $this->conn->commit();
            return ['success' => true, 'message' => 'Booking updated successfully'];
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Admin manage booking error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update booking details'];
        }
    }

    private function markReminderAsSent($bookingId, $daysLeft) {
        $column = $daysLeft === 10 ? 'reminder_10_day_email_sent' : ($daysLeft === 3 ? 'reminder_3_day_email_sent' : 'reminder_24_hour_email_sent');
        $stmt = $this->conn->prepare("UPDATE bookings SET {$column} = 1 WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $bookingId);
            $stmt->execute();
            $stmt->close();
        }
    }

    private function ensureIntegrityConstraints() {
        if (self::$integrityChecked) {
            return;
        }

        $this->ensureEmailReminderColumns();
        self::$integrityChecked = true;
    }

    private function ensureEmailReminderColumns() {
        if (self::$emailColumnsChecked) {
            return;
        }

        $columns = [
            'notification_preference' => "ALTER TABLE bookings ADD COLUMN notification_preference ENUM('sms','email','both') NOT NULL DEFAULT 'both' AFTER special_requests",
            'confirmation_email_sent' => "ALTER TABLE bookings ADD COLUMN confirmation_email_sent TINYINT(1) DEFAULT 0",
            'reminder_10_day_email_sent' => "ALTER TABLE bookings ADD COLUMN reminder_10_day_email_sent TINYINT(1) DEFAULT 0",
            'reminder_3_day_email_sent' => "ALTER TABLE bookings ADD COLUMN reminder_3_day_email_sent TINYINT(1) DEFAULT 0",
            'reminder_24_hour_email_sent' => "ALTER TABLE bookings ADD COLUMN reminder_24_hour_email_sent TINYINT(1) DEFAULT 0",
            'cancelled_at' => "ALTER TABLE bookings ADD COLUMN cancelled_at TIMESTAMP NULL DEFAULT NULL AFTER status"
        ];

        foreach ($columns as $column => $alterSql) {
            if (!$this->columnExists('bookings', $column)) {
                $this->conn->query($alterSql);
            }
        }

        self::$emailColumnsChecked = true;
    }

    private function columnExists($table, $column) {
        $table = $this->conn->real_escape_string($table);
        $column = $this->conn->real_escape_string($column);
        $result = $this->conn->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        if (!$result) {
            return false;
        }
        return $result->num_rows > 0;
    }

    private function getBookingPhoneColumn() {
        if (self::$bookingPhoneColumn !== null) {
            return self::$bookingPhoneColumn;
        }

        if ($this->columnExists('bookings', 'devotee_phone')) {
            self::$bookingPhoneColumn = 'devotee_phone';
            return self::$bookingPhoneColumn;
        }

        if ($this->columnExists('bookings', 'phone')) {
            self::$bookingPhoneColumn = 'phone';
            return self::$bookingPhoneColumn;
        }

        // Best-effort fallback for legacy DBs.
        self::$bookingPhoneColumn = 'devotee_phone';
        return self::$bookingPhoneColumn;
    }
}