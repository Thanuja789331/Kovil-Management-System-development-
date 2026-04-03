<?php
/**
 * Bootstrap File - Application Initialization
 * 
 * This file handles all session configuration and initialization
 * before any application code runs. All session settings must be
 * configured BEFORE calling session_start().
 */

// Prevent direct access
defined('APP_NAME') or define('APP_NAME', 'Kovil Management System');

/**
 * Initialize Session with Security Settings
 * 
 * Configures PHP session settings for security and compatibility.
 * Must be called before session_start() to avoid warnings.
 */
function initializeSession(): void {
    // Check if session is already active to prevent duplicate initialization
    if (session_status() === PHP_SESSION_ACTIVE) {
        return; // Session already active, nothing to do
    }
    
    // ===========================================
    // SESSION SECURITY CONFIGURATION
    // ===========================================
    // These MUST be set before session_start()
    
    // Prevent JavaScript from accessing session cookie (XSS protection)
    ini_set('session.cookie_httponly', '1');
    
    // Force cookies to be used (disable URL-based sessions)
    ini_set('session.use_only_cookies', '1');
    
    // Enforce strict mode - prevents session ID injection attacks
    ini_set('session.use_strict_mode', '1');
    
    // Set secure cookie parameters (adjust for production)
    $cookieParams = [
        'lifetime' => 0,           // Session expires when browser closes
        'path' => '/',             // Available throughout the domain
        'domain' => '',            // Current domain only
        'secure' => false,         // Set to true in production with HTTPS
        'httponly' => true,        // Prevent JavaScript access
        'samesite' => 'Strict'     // CSRF protection (PHP 7.3+)
    ];
    
    // Apply cookie parameters
    session_set_cookie_params($cookieParams);
    
    // ===========================================
    // START SESSION
    // ===========================================
    session_start();
    
    // Regenerate session ID periodically to prevent fixation attacks
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// ===========================================
// INITIALIZE APPLICATION
// ===========================================
// Load configuration first
require_once __DIR__ . '/config.php';

// Then initialize session
initializeSession();

// Load helper functions (optional, for advanced session management)
require_once __DIR__ . '/helpers.php';

// Load SMS/Notification helper
require_once __DIR__ . '/sms_helper.php';
