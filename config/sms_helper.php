<?php
/**
 * SMS/Notification Helper Functions
 * For production, integrate with actual SMS gateway APIs like Twilio, MSG91, etc.
 */

/**
 * Send SMS notification
 * 
 * @param string $phone Phone number (10-digit or international format)
 * @param string $message Message content
 * @param string $type Type of notification (booking_confirmation, duty_assignment, registration_approval)
 * @return array Result with success status and message
 */
function sendSMS($phone, $message, $type = 'general') {
    // Log the notification attempt
    $logId = logNotification($phone, $message, $type, 'pending');
    
    // In production, integrate with SMS gateway API here
    // Examples: Twilio, MSG91, TextLocal, etc.
    
    // For now, we'll simulate SMS sending
    $success = simulateSMSSending($phone, $message);
    
    // Update log with result
    $status = $success ? 'sent' : 'failed';
    updateNotificationLog($logId, $status);
    
    // Additional logging for booking confirmations
    if ($type === 'booking_confirmation') {
        error_log("Booking Confirmation SMS sent to {$phone} - Log ID: {$logId}");
    }
    
    return [
        'success' => $success,
        'message' => $success ? "Confirmation sent to {$phone}" : "Failed to send SMS",
        'log_id' => $logId
    ];
}

/**
 * Simulate SMS sending (replace with actual SMS gateway integration)
 */
function simulateSMSSending($phone, $message) {
    // Log SMS for debugging
    error_log("SMS to $phone: " . substr($message, 0, 100) . "...");
    
    // Simulate success (in development)
    // In production, replace with actual API call
    return true;
}

/**
 * Log notification to database
 */
function logNotification($phone, $message, $type, $status = 'pending') {
    try {
        $conn = Database::connect();
        $stmt = $conn->prepare("INSERT INTO notification_logs (recipient_phone, message_type, message_content, status) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $phone, $type, $message, $status);
            $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            return $insertId;
        }
    } catch (Exception $e) {
        error_log("Log notification error: " . $e->getMessage());
    }
    return null;
}

/**
 * Update notification log status
 */
function updateNotificationLog($logId, $status) {
    try {
        $conn = Database::connect();
        $stmt = $conn->prepare("UPDATE notification_logs SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $logId);
            $stmt->execute();
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log("Update notification log error: " . $e->getMessage());
    }
}

/**
 * Send duty assignment notification to priest
 */
function sendDutyAssignmentSMS($priestPhone, $priestName, $poojaName, $poojaDate, $timeSlot) {
    $message = "New Duty Assignment\n";
    $message .= "Priest: $priestName\n";
    $message .= "Pooja: $poojaName\n";
    $message .= "Date: " . date("M j, Y", strtotime($poojaDate)) . "\n";
    $message .= "Time: $timeSlot\n";
    $message .= "Please be available.";
    
    return sendSMS($priestPhone, $message, 'duty_assignment');
}

/**
 * Validate phone number format
 */
function validatePhoneNumber($phone) {
    // Remove spaces, dashes, and parentheses
    $cleaned = preg_replace('/[\s\-\(\)]/', '', $phone);
    
    // Check if it's a valid 10-digit Indian number or international format
    if (preg_match('/^[0-9]{10}$/', $cleaned)) {
        return $cleaned; // Valid 10-digit number
    } elseif (preg_match('/^\+[0-9]{1,3}[0-9]{10}$/', $cleaned)) {
        return $cleaned; // Valid international format
    }
    
    return false;
}

/**
 * Check if string starts with given prefix
 */
function startsWith($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

/**
 * Format phone number for display
 */
function formatPhoneNumber($phone) {
    $cleaned = preg_replace('/[^0-9+]/', '', $phone);
    
    if (strlen($cleaned) === 10) {
        // Format as XXXXX-XXXXX
        return substr($cleaned, 0, 5) . '-' . substr($cleaned, 5);
    } elseif (strlen($cleaned) === 13 && startsWith($cleaned, '+91')) {
        // Format as +91 XXXXX XXXXX
        return '+91 ' . substr($cleaned, 3, 5) . ' ' . substr($cleaned, 8);
    }
    
    return $cleaned;
}
