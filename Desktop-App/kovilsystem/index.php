<?php
/**
 * Application Entry Point
 * 
 * All requests are routed through this file.
 * Session initialization is handled by bootstrap.php
 */

// ===========================================
// LOAD BOOTSTRAP (Session + Config)
// ===========================================
require_once __DIR__ . "/config/bootstrap.php";

// ===========================================
// LOAD DATABASE CONNECTION
// ===========================================
require_once __DIR__ . "/config/database.php";

// ===========================================
// LOAD CONTROLLER
// ===========================================
require_once __DIR__ . "/app/controllers/MainController.php";

// ===========================================
// ROUTING
// ===========================================
// Get requested page, default to home (landing page)
$page = $_GET['url'] ?? 'home';

// Sanitize input to prevent XSS attacks
$page = htmlspecialchars(strip_tags($page), ENT_QUOTES, 'UTF-8');

// ===========================================
// INITIALIZE APPLICATION
// ===========================================
try {
    $controller = new MainController();
    $controller->load($page);
} catch (Exception $e) {
    // Log error for debugging (in production, log to file instead)
    error_log("Application Error: " . $e->getMessage());
    
    // Show user-friendly error message
    if (defined('APP_ENV') && APP_ENV === 'development') {
        echo "<div style='padding: 20px; color: red;'>";
        echo "<h2>Error Occurred</h2>";
        echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "</div>";
    } else {
        echo "<div style='padding: 20px;'>";
        echo "<h2>An error occurred. Please try again later.</h2>";
        echo "</div>";
    }
}