<?php
/**
 * Application Configuration File
 * 
 * Contains database and application settings.
 * Session configuration has been moved to bootstrap.php
 * to ensure proper initialization order.
 */

// ===========================================
// DATABASE CONFIGURATION
// ===========================================
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "kovil_db");
define("DB_CHARSET", "utf8mb4");

// ===========================================
// APPLICATION CONFIGURATION
// ===========================================
// APP_NAME is defined in bootstrap.php if not already set
if (!defined('APP_NAME')) {
    define("APP_NAME", "Kovil Management System");
}
define("APP_URL", "http://localhost/kovilSystem_fixed");
define("APP_ENV", "development"); // Change to 'production' in live environment

// ===========================================
// ERROR REPORTING (Development vs Production)
// ===========================================
if (defined('APP_ENV') && APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}