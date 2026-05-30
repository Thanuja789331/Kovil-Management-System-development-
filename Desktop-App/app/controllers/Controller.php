<?php

/**
 * Base Controller class with common methods
 */
class Controller {
    protected $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Check if user has the required role
     */
    protected function checkRole($requiredRole) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $requiredRole) {
            header("Location: ?url=login");
            exit;
        }
    }

    /**
     * Load a view with header and footer
     */
    protected function loadView($view, $data = null) {
        require __DIR__ . "/../views/layouts/header.php";
        
        if ($data !== null) {
            extract($data);
        }
        
        if (file_exists($view)) {
            require $view;
        } else {
            echo "<div class='p-10 text-center text-white'>View not found: $view</div>";
        }
        
        require __DIR__ . "/../views/layouts/footer.php";
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    /**
     * Set session message
     */
    protected function setSessionMessage($type, $message) {
        $_SESSION[$type] = $message;
    }

    /**
     * Get and clear session message
     */
    protected function getSessionMessage($type) {
        if (isset($_SESSION[$type])) {
            $message = $_SESSION[$type];
            unset($_SESSION[$type]);
            return $message;
        }
        return null;
    }
}
