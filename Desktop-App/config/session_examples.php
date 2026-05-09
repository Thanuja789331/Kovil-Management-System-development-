<?php
/**
 * Safe Session Usage - Quick Reference
 * 
 * This file demonstrates how to properly use sessions in your PHP MVC project.
 * All examples below are production-ready and follow best practices.
 */

// ===========================================
// EXAMPLE 1: Basic Session Usage
// ===========================================

// Sessions are automatically started by bootstrap.php
// You can use $_SESSION directly anywhere in your code:

// In controllers:
$_SESSION['user'] = $userData;           // ✅ Safe to use
$userId = $_SESSION['user']['id'];       // ✅ Safe to read

// ===========================================
// EXAMPLE 2: Using Helper Functions (Recommended)
// ===========================================

require_once __DIR__ . '/config/helpers.php';

// Set session data
setSession('cart', ['item1', 'item2']);

// Get session data
$cart = getSession('cart', []);  // Returns empty array if not set

// Check if exists
if (hasSession('user')) {
    echo "User is logged in";
}

// Remove session data
unsetSession('temporary_data');

// ===========================================
// EXAMPLE 3: Flash Messages (One-time Messages)
// ===========================================

// After form submission, set a flash message
setFlash('success', 'Item added to cart successfully!');
// or
setFlash('error', 'Payment failed. Please try again.');

// In your view file, display the flash message:
$flash = getFlash();
if ($flash) {
    echo "<div class='alert alert-{$flash['type']}'>";
    echo htmlspecialchars($flash['message']);
    echo "</div>";
}
// Message automatically cleared after being read

// ===========================================
// EXAMPLE 4: User Authentication
// ===========================================

// Check if logged in
if (isLoggedIn()) {
    $user = getCurrentUser();
    echo "Welcome, " . htmlspecialchars($user['name']);
} else {
    header("Location: ?url=login");
    exit;
}

// Require authentication (at top of controller method)
requireAuth();  // Redirects to login if not authenticated

// Require specific role
requireRole('management');  // Only allows management role
requireRole('priest');      // Only allows priest role
requireRole('devotee');     // Only allows devotee role

// ===========================================
// EXAMPLE 5: Secure Login Implementation
// ===========================================

/*
 * REAL-WORLD EXAMPLE (Copy this pattern):
 * 
 * In your MainController.php login case:
 * 
 * case 'login':
 *     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 *         $email = trim($_POST['email'] ?? '');
 *         $password = $_POST['password'] ?? '';
 *         
 *         // Use your actual model
 *         $user = $userModel->login($email, $password);
 *         
 *         if ($user) {
 *             // Regenerate session ID (prevents session fixation)
 *             if (function_exists('regenerateSessionId')) {
 *                 regenerateSessionId();
 *             } else {
 *                 session_regenerate_id(true);
 *             }
 *             
 *             // Store user data
 *             $_SESSION['user'] = $user;
 *             $_SESSION['login_time'] = time();
 *             
 *             // Redirect based on role
 *             switch ($user['role']) {
 *                 case 'management':
 *                     header("Location: ?url=dashboard");
 *                     break;
 *                 case 'devotee':
 *                     header("Location: ?url=schedule");
 *                     break;
 *                 case 'priest':
 *                     header("Location: ?url=priest");
 *                     break;
 *             }
 *             exit;
 *         } else {
 *             $error = "Invalid email or password";
 *         }
 *     }
 *     break;
 */

// Theoretical example (for illustration only - don't use directly)
/*
function handleLogin($email, $password) {
    // Your validation logic here
    $user = /* your_validation_function($email, $password); *\/
    
    if ($user) {
        regenerateSessionId();
        setSession('user', $user);
        setSession('login_time', time());
        header("Location: ?url=dashboard");
        exit;
    }
}
*/

// ===========================================
// EXAMPLE 6: Secure Logout Implementation
// ===========================================

function handleLogout() {
    // Properly destroy all session data
    destroySession();
    
    // Redirect to login
    header("Location: ?url=login");
    exit;
}

// ===========================================
// EXAMPLE 7: Session Timeout (Advanced)
// ===========================================

// Check for session timeout (30 minutes)
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes in seconds
    
    if (hasSession('last_activity')) {
        $lastActivity = getSession('last_activity');
        $currentTime = time();
        
        if (($currentTime - $lastActivity) > $timeout) {
            // Session expired
            destroySession();
            setFlash('error', 'Your session has expired. Please login again.');
            header("Location: ?url=login");
            exit;
        }
    }
    
    // Update last activity
    setSession('last_activity', time());
}

// Call this function at the start of each protected page
checkSessionTimeout();

// ===========================================
// EXAMPLE 8: CSRF Token Generation (Security)
// ===========================================

// Generate CSRF token
function generateCsrfToken() {
    if (!hasSession('csrf_token')) {
        $token = bin2hex(random_bytes(32));
        setSession('csrf_token', $token);
    }
    return getSession('csrf_token');
}

// Verify CSRF token
function verifyCsrfToken($token) {
    $storedToken = getSession('csrf_token');
    return hash_equals($storedToken, $token);
}

// Usage in forms:
// <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

// Usage in form processing:
// if (!verifyCsrfToken($_POST['csrf_token'])) {
//     die('CSRF validation failed');
// }

// ===========================================
// EXAMPLE 9: Multiple User Sessions (Optional)
// ===========================================

// Store multiple session data
setSession('user_preferences', ['theme' => 'dark', 'language' => 'en']);
setSession('shopping_cart', []);
setSession('wishlist', []);

// Access different session namespaces
$prefs = getSession('user_preferences');
$cart = getSession('shopping_cart');

// ===========================================
// EXAMPLE 10: Debugging Session Issues
// ===========================================

// Check session status
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "Session is active";
} elseif (session_status() === PHP_SESSION_NONE) {
    echo "Session is not started";
} elseif (session_status() === PHP_SESSION_DISABLED) {
    echo "Sessions are disabled";
}

// View all session data (for debugging)
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

// ===========================================
// DO's and DON'Ts
// ===========================================

/*
✅ DO:
- Use helper functions for cleaner code
- Regenerate session ID after login
- Use requireAuth() to protect pages
- Clear sessions properly on logout
- Use flash messages for user feedback
- Check session_status() before manual operations

❌ DON'T:
- Call session_start() manually (bootstrap does it)
- Store sensitive data like passwords in session
- Use sessions without checking if they exist
- Forget to escape output from sessions (XSS risk)
- Modify session settings after session has started
*/

// ===========================================
// Common Use Cases in Your Project
// ===========================================

// In MainController - Login case:
case 'login':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $userModel->login($email, $password);
        if ($user) {
            regenerateSessionId();  // ✅ Security
            $_SESSION['user'] = $user;
            header("Location: ?url=dashboard");
        }
    }
    break;

// In MainController - Protected page:
case 'dashboard':
    if (!isset($_SESSION['user'])) {  // ✅ Check auth
        header("Location: ?url=login");
        exit;
    }
    // Show dashboard...
    break;

// In MainController - Logout:
case 'logout':
    destroySession();  // ✅ Proper cleanup
    header("Location: ?url=login");
    break;
