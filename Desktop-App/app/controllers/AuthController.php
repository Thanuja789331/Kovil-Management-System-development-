<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/Controller.php";

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Handle login
     */
    public function login() {
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Please enter both email and password";
            } else {
                $user = $this->userModel->login($email, $password);

                if ($user) {
                    // Regenerate session ID to prevent session fixation attacks
                    if (function_exists('regenerateSessionId')) {
                        regenerateSessionId();
                    } else {
                        session_regenerate_id(true);
                    }
                    
                    // Store user in session
                    $_SESSION['user'] = $user;
                    $_SESSION['login_time'] = time();
                    $_SESSION['user_role'] = $user['role']; // Explicitly store role
                    
                    // Debug: Log successful login (remove in production)
                    error_log("Login successful for {$email}. Role: {$user['role']}");

                    // All roles go to dashboard, which is now role-specific
                    $this->redirect("?url=dashboard");
                } else {
                    // Check if user exists but is pending approval
                    $checkUser = $this->userModel->getByEmail($email);
                    if ($checkUser && $checkUser['approval_status'] === 'pending') {
                        $error = "<div>⏳ Your account is pending admin approval. Please wait for approval before logging in.</div><div class='mt-2'><small>Admin will review your registration shortly.</small></div>";
                    } elseif ($checkUser && $checkUser['approval_status'] === 'rejected') {
                        $error = "<div>❌ Your account has been rejected. Please contact admin for more information.</div>";
                    } elseif ($checkUser) {
                        // User exists but wrong password or role mismatch
                        $error = "<div>❌ Invalid password. Please try again.</div><div class='mt-2'><small>Hint: Default password is 'password'</small></div>";
                    } else {
                        // User doesn't exist at all
                        $error = "<div>❌ No account found with this email address.</div><div class='mt-2'><small>Please register first or check your email.</small></div>";
                    }
                }
            }
        }

        return ['error' => $error];
    }

    /**
     * Handle registration
     */
    public function register() {
        $message = "";
        $messageType = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'devotee';
            $phone = trim($_POST['phone'] ?? '');

            if (empty($name) || empty($email) || empty($password)) {
                $message = "All fields are required";
                $messageType = "error";
            } elseif ($password !== $confirmPassword) {
                $message = "Passwords do not match";
                $messageType = "error";
            } elseif (strlen($password) < 6) {
                $message = "Password must be at least 6 characters";
                $messageType = "error";
            } elseif (!empty($phone) && !$this->userModel->validatePhone($phone)) {
                $message = "Please enter a valid phone number";
                $messageType = "error";
            } else {
                $result = $this->userModel->register($name, $email, $password, $role, $phone);
                
                if ($result['success']) {
                    // Set success message in session and redirect to login
                    $_SESSION['registration_success'] = true;
                    $_SESSION['registration_message'] = $result['message'];
                    
                    // Redirect to login page
                    $this->redirect("?url=login");
                } else {
                    $message = $result['message'];
                    $messageType = "error";
                }
            }
        }
        
        // Check for success message from POST redirect
        if (isset($_SESSION['registration_success'])) {
            $message = $_SESSION['registration_message'] ?? 'Registration successful!';
            $messageType = 'success';
            unset($_SESSION['registration_success']);
            unset($_SESSION['registration_message']);
        }

        return ['message' => $message, 'messageType' => $messageType];
    }

    /**
     * Handle logout
     */
    public function logout() {
        // Properly destroy session using helper function
        if (function_exists('destroySession')) {
            destroySession();
        } else {
            // Fallback if helpers not loaded
            $_SESSION = [];
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
            session_destroy();
        }
        $this->redirect("?url=login");
    }

    /**
     * Handle forgot password
     */
    public function forgotPassword() {
        $message = "";
        $messageType = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $reset = $this->userModel->createPasswordResetToken($email, 30);
                if (!empty($reset['success'])) {
                    $resetUrl = buildAppUrl("?url=reset-password&token=" . urlencode($reset['token']));
                    $subject = "Password Reset Request";
                    $body = "Hello {$reset['user']['name']},\n\nA password reset was requested for your account.\n\nReset Link:\n{$resetUrl}\n\nThis link expires in 30 minutes.\nIf you did not request this, you can ignore this email.";
                    sendEmailNotification($reset['user']['email'], $subject, $body, 'password_reset');
                }
            }
            $message = "If your email is registered, a password reset link has been sent.";
            $messageType = "success";
        }

        return ['message' => $message, 'messageType' => $messageType];
    }

    /**
     * Handle reset password
     */
    public function resetPassword() {
        $error = "";
        $data = null;

        $token = trim($_GET['token'] ?? $_POST['token'] ?? '');
        if ($token === '') {
            return ['error' => "Reset token is missing.", 'data' => null];
        }

        $tokenData = $this->userModel->getValidPasswordReset($token);
        if (!$tokenData) {
            return ['error' => "This reset link is invalid or expired.", 'data' => null];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            if (strlen($newPassword) < 6) {
                $error = "Password must be at least 6 characters.";
            } elseif ($newPassword !== $confirmPassword) {
                $error = "Passwords do not match.";
            } else {
                $result = $this->userModel->resetPasswordWithToken($token, $newPassword);
                if ($result['success']) {
                    $_SESSION['registration_success'] = true;
                    $_SESSION['registration_message'] = "Password reset successful. Please login.";
                    $this->redirect("?url=login");
                }
                $error = $result['message'];
            }
        }
        $data = ['token' => $token, 'user_email' => $tokenData['email']];

        return ['error' => $error, 'data' => $data];
    }
}
