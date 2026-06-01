<?php
/**
 * Email helper.
 *
 * Uses PHP's built-in mail() so it works wherever Sendmail/SMTP is configured
 * (WAMP uses Sendmail by default; set SMTP in php.ini for a real mail server).
 * Falls back to error_log if mail() is disabled so reminders are still marked
 * sent and never retried endlessly.
 */

function sendEmailNotification(string $to, string $subject, string $message, string $type = 'general'): array {
    $to = trim((string) $to);
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Invalid email address'];
    }

    $appName = defined('APP_NAME') ? APP_NAME : 'Kovil Management System';
    $fromEmail = 'noreply@kovil.local';

    $headers = implode("\r\n", [
        "From: {$appName} <{$fromEmail}>",
        "Reply-To: {$fromEmail}",
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: PHP/' . PHP_VERSION,
        'X-Notification-Type: ' . $type,
    ]);

    $sent = function_exists('mail') && @mail($to, $subject, $message, $headers);

    // Always log for audit trail regardless of delivery outcome
    error_log("EMAIL[{$type}] TO={$to} SUBJECT={$subject} DELIVERED=" . ($sent ? 'yes' : 'no(logged-only)'));

    // Return success=true even when mail() fails so the reminder is marked
    // as sent in the DB and not retried on every page load.
    return [
        'success' => true,
        'message' => $sent
            ? "Email sent to {$to}"
            : "Email logged for {$to} — configure SMTP in php.ini for real delivery",
    ];
}

function buildAppUrl(string $path): string {
    $base = defined('APP_URL') ? rtrim(APP_URL, '/') : '';
    $path = ltrim((string) $path, '/');
    return $base . '/' . $path;
}
