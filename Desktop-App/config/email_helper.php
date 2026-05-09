<?php
/**
 * Email helper (development/log mode).
 *
 * This keeps current behavior stable and provides a single place to
 * switch to SMTP/API delivery later.
 */

function sendEmailNotification($to, $subject, $message, $type = 'general') {
    $to = trim((string) $to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'message' => 'Invalid email address'
        ];
    }

    // Development delivery mode: log outbound email payload.
    error_log("EMAIL[$type] TO={$to} SUBJECT={$subject} BODY=" . str_replace(["\r", "\n"], ' ', $message));

    return [
        'success' => true,
        'message' => "Email queued for {$to} (dev log mode)"
    ];
}

function buildAppUrl($path) {
    $base = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
    $path = ltrim((string) $path, '/');
    return $base . '/' . $path;
}
