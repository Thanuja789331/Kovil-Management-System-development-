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

// Load Composer autoloader (required for league/oauth2-google and other packages)
$_composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($_composerAutoload)) {
    require_once $_composerAutoload;
}
unset($_composerAutoload);

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
    // SameSite=Lax (not Strict) is required for OAuth flows: Google redirects back
    // to the app as a cross-site navigation, and Strict would block the session cookie.
    // Detect if connection is via HTTPS (check both standard and load balancer headers)
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
             || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
             || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
             || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443);
    
    // Set secure cookie parameters
    // SameSite=Lax (not Strict) is required for OAuth flows: Google redirects back
    // to the app as a cross-site navigation, and Strict would block the session cookie.
    $cookieParams = [
        'lifetime' => 0,           // Session expires when browser closes
        'path' => '/',             // Available throughout the domain
        'domain' => '',            // Current domain only
        'secure' => ($isSecure || (defined('APP_ENV') && APP_ENV === 'production')),  // HTTPS enforced in production
        'httponly' => true,        // Prevent JavaScript access
        'samesite' => 'Lax'        // Allows cross-site top-level navigations (needed for OAuth)
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
    
    // ===========================================
    // SESSION INACTIVITY TIMEOUT (30 minutes)
    // ===========================================
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
        // Session has been idle for more than 30 minutes — destroy it
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(
                session_name(),
                '',
                time() - 3600,
                session_get_cookie_params()['path'],
                session_get_cookie_params()['domain'],
                session_get_cookie_params()['secure'],
                session_get_cookie_params()['httponly']
            );
        }
        session_destroy();
        session_start(); // Restart with a clean session
    }
    $_SESSION['last_activity'] = time(); // Update activity timestamp
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

// Load Email helper (development log mode)
require_once __DIR__ . '/email_helper.php';
