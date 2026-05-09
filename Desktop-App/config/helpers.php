<?php
/**
 * Session Helper Functions
 * 
 * Provides safe, reusable session operations throughout the application.
 * All functions check session status before performing operations.
 */


/**
 * Set a session variable
 * 
 * @param string $key   The session key
 * @param mixed  $value The value to store
 * @return void
 */
function setSession(string $key, $value): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION[$key] = $value;
    } else {
        error_log("Attempted to set session '$key' but session is not active");
    }
}

/**
 * Get a session variable
 * 
 * @param string $key     The session key
 * @param mixed  $default Default value if not found
 * @return mixed          The stored value or default
 */
function getSession(string $key, $default = null) {
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$key])) {
        return $_SESSION[$key];
    }
    return $default;
}

/**
 * Check if a session variable exists
 * 
 * @param string $key The session key
 * @return bool       True if exists, false otherwise
 */
function hasSession(string $key): bool {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return isset($_SESSION[$key]);
    }
    return false;
}

/**
 * Remove a session variable
 * 
 * @param string $key The session key
 * @return void
 */
function unsetSession(string $key): void {
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

/**
 * Set flash message (one-time message)
 * 
 * @param string $type    Message type (success, error, warning, info)
 * @param string $message The message content
 * @return void
 */
function setFlash(string $type, string $message): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

/**
 * Get and clear flash message
 * 
 * @return array|null Flash data or null if not set
 */
function getFlash(): ?array {
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Regenerate session ID (security measure)
 * 
 * Call this after login to prevent session fixation
 * 
 * @return bool True on success, false on failure
 */
function regenerateSessionId(): bool {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return session_regenerate_id(true);
    }
    return false;
}

/**
 * Destroy session completely
 * 
 * Properly clears all session data and destroys the session
 * 
 * @return void
 */
function destroySession(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Clear all session variables
        $_SESSION = [];
        
        // Delete the session cookie
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
        
        // Destroy the session
        session_destroy();
    }
}

/**
 * Check if user is logged in
 * 
 * @return bool True if authenticated, false otherwise
 */
function isLoggedIn(): bool {
    return hasSession('user') && is_array(getSession('user'));
}

/**
 * Get current logged-in user
 * 
 * @return array|null User data or null if not logged in
 */
function getCurrentUser(): ?array {
    return getSession('user');
}

/**
 * Require authentication - redirect to login if not logged in
 * 
 * @return void
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        header("Location: ?url=login");
        exit;
    }
}

/**
 * Require specific role - redirect if user doesn't have required role
 * 
 * @param string $role Required role
 * @return void
 */
function requireRole(string $role): void {
    requireAuth();
    
    $user = getCurrentUser();
    if (!isset($user['role']) || $user['role'] !== $role) {
        header("Location: ?url=login");
        exit;
    }
}

/**
 * Get translation for a key
 * 
 * @param string $key Translation key
 * @param string|null $lang Language code (en, ta). If null, uses session language
 * @return string Translated text or English fallback
 */
function trans(string $key, ?string $lang = null): string {
    static $translations = null;
    
    // Load translations if not already loaded
    if ($translations === null) {
        $translations = require __DIR__ . '/translations.php';
    }
    
    // Get language from session if not provided
    if ($lang === null) {
        $lang = getSession('language', 'en');
    }
    
    // Return translation or fallback to English
    if (isset($translations[$key][$lang])) {
        return $translations[$key][$lang];
    }
    
    // Fallback to English if translation not found
    if (isset($translations[$key]['en'])) {
        return $translations[$key]['en'];
    }
    
    // Return key if no translation found
    return $key;
}

/**
 * Set user language preference
 * 
 * @param string $lang Language code (en, ta)
 * @return void
 */
function setLanguage(string $lang): void {
    $supportedLanguages = ['en', 'ta'];
    if (in_array($lang, $supportedLanguages)) {
        setSession('language', $lang);
    }
}

/**
 * Get current language
 * 
 * @return string Current language code
 */
function getCurrentLanguage(): string {
    return getSession('language', 'en');
}

/**
 * Get language name from code
 * 
 * @param string $code Language code
 * @return string Language name
 */
function getLanguageName(string $code): string {
    $languages = [
        'en' => 'English',
        'ta' => 'தமிழ்'
    ];
    return $languages[$code] ?? 'English';
}
