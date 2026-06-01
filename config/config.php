<?php

/**
 * Application Configuration File
 * 
 * Contains database and application settings.
 * Session configuration has been moved to bootstrap.php
 * to ensure proper initialization order.
 */

// ===========================================
// DATABASE CONFIGURATION (Environment-based for AWS compatibility)
// ===========================================
define("DB_HOST", getenv('DB_HOST') ?: "localhost");
define("DB_USER", getenv('DB_USER') ?: "root");
define("DB_PASS", getenv('DB_PASS') ?: "");
define("DB_NAME", getenv('DB_NAME') ?: "kovil_db");
define("DB_CHARSET", "utf8mb4");
define("DB_PORT", getenv('DB_PORT') ?: 3306);

// ===========================================
// APPLICATION CONFIGURATION
// ===========================================
// APP_NAME is defined in bootstrap.php if not already set
if (!defined('APP_NAME')) {
    define("APP_NAME", "Kovil Management System");
}
define("APP_URL", getenv('APP_URL') ?: "http://localhost/Kovil_System");
define("APP_ENV", getenv('APP_ENV') ?: "development"); // Change to 'production' in live environment
define("UPLOAD_PATH", dirname(__DIR__) . "/public/uploads/");
define("UPLOAD_URL",  APP_URL . "/public/uploads/");

// ===========================================
// GOOGLE OAUTH CONFIGURATION
// Get credentials from Google Cloud Console:
// https://console.cloud.google.com → APIs & Services → Credentials
// Store these in AWS Elastic Beanstalk Environment Properties
// ===========================================
define("GOOGLE_CLIENT_ID",     getenv('GOOGLE_CLIENT_ID') ?: '');
define("GOOGLE_CLIENT_SECRET", getenv('GOOGLE_CLIENT_SECRET') ?: '');
define("GOOGLE_REDIRECT_URI",  APP_URL . "/?url=oauth-callback");

// Validate Google OAuth is configured (warning in logs if not)
if (defined('APP_ENV') && APP_ENV === 'production') {
    if (empty(GOOGLE_CLIENT_ID) || empty(GOOGLE_CLIENT_SECRET)) {
        error_log("[WARNING] Google OAuth credentials not configured. OAuth login will be unavailable.");
    }
}

// ===========================================
// ERROR REPORTING (Development vs Production)
// ===========================================
if (!defined('APP_ENV')) {
    define('APP_ENV', 'development');
}

if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
} else {
    // Production: Log errors to file, don't display
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__DIR__) . '/logs/php-errors.log');
}

// Always log errors even in development
ini_set('log_errors', 1);

// Validate critical configuration in production
if (APP_ENV === 'production') {
    // In production, Google OAuth credentials MUST be set
    if (empty(getenv('GOOGLE_CLIENT_ID')) || empty(getenv('GOOGLE_CLIENT_SECRET'))) {
        error_log("WARNING: Google OAuth credentials not configured. OAuth login will fail.");
    }
}
