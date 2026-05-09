<?php
/**
 * Database Connection Class
 * 
 * Note: This file should be loaded AFTER bootstrap.php
 * which initializes configuration and sessions.
 */

class Database {
    private static $conn = null;

    public static function connect() {
        // Check if constants are defined (bootstrap should be loaded first)
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/config.php';
        }
        
        if (self::$conn === null) {
            try {
                self::$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                if (self::$conn->connect_error) {
                    throw new Exception("Database Connection Error: " . self::$conn->connect_error);
                }
                
                self::$conn->set_charset(DB_CHARSET);
            } catch (Exception $e) {
                // Log error and show user-friendly message
                error_log("Database Error: " . $e->getMessage());
                die("Database connection failed. Please ensure the database is configured properly.");
            }
        }
        
        return self::$conn;
    }
    
    public static function close() {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }
}