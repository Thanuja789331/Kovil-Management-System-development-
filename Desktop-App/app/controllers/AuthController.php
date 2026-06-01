<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/Controller.php";

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    // ------------------------------------------------------------------
    // LOGIN — validates credentials; triggers 2FA for devotee/priest
    // ------------------------------------------------------------------
    public function login() {
        $error = "";

        // Flash errors from OAuth redirects or expired 2FA sessions
        foreach (['login_error', '2fa_error'] as $key) {
            if (!empty($_SESSION[$key])) {
                $error = $_SESSION[$key];
                unset($_SESSION[$key]);
                break;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Please enter both email and password";
            } else {
                $user = $this->userModel->login($email, $password);

                if ($user && !empty($user['id'])) {
                    error_log("Login successful for {$email}. Role: {$user['role']}");

                    $needs2fa = in_array($user['role'], ['devotee', 'priest'], true)
                                && !empty($user['two_factor_enabled']);

                    if ($needs2fa) {
                        // 2FA enabled: send OTP and hold login until verified
                        $this->generateAndSendOtp($user);
                        $this->redirect("?url=verify-2fa");
                    } else {
                        $this->completeLogin($user);
                    }
                } else {
                    $checkUser = $this->userModel->getByEmail($email);
                    if ($checkUser && $checkUser['approval_status'] === 'pending') {
                        $error = "<div>&#9203; Your account is pending admin approval. Please wait for approval before logging in.</div><div class='mt-2'><small>Admin will review your registration shortly.</small></div>";
                    } elseif ($checkUser && $checkUser['approval_status'] === 'rejected') {
                        $error = "<div>&#10060; Your account has been rejected. Please contact admin for more information.</div>";
                    } elseif ($checkUser) {
                        $error = "<div>&#10060; Invalid password. Please try again.</div>";
                    } else {
                        $error = "<div>&#10060; No account found with this email address.</div>";
                    }
                }
            }
        }

        return ['error' => $error];
    }

    // ------------------------------------------------------------------
    // VERIFY 2FA — checks the OTP the user typed in
    // ------------------------------------------------------------------
    public function verify2fa() {
        // If already logged in, go to dashboard
        if (!empty($_SESSION['user']['id'])) {
            $this->redirect("?url=dashboard");
        }

        // Must have a pending 2FA session
        if (empty($_SESSION['2fa_pending'])) {
            $this->redirect("?url=login");
        }

        $pending =& $_SESSION['2fa_pending'];
        $error   = "";
        $info    = "";

        // Collect any info flash (e.g. "Code resent")
        if (!empty($_SESSION['2fa_info'])) {
            $info = $_SESSION['2fa_info'];
            unset($_SESSION['2fa_info']);
        }

        // Check expiry
        if (time() > $pending['expires_at']) {
            unset($_SESSION['2fa_pending']);
            $_SESSION['2fa_error'] = "Your verification code has expired. Please log in again.";
            $this->redirect("?url=login");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = preg_replace('/\s+/', '', $_POST['otp_code'] ?? '');

            if (strlen($code) !== 6 || !ctype_digit($code)) {
                $error = "Please enter the 6-digit code sent to your email.";
            } elseif ($pending['attempts'] >= 5) {
                unset($_SESSION['2fa_pending']);
                $_SESSION['2fa_error'] = "Too many failed attempts. Please log in again.";
                $this->redirect("?url=login");
            } elseif (!hash_equals($pending['otp_hash'], hash('sha256', $code))) {
                $pending['attempts']++;
                $left  = 5 - $pending['attempts'];
                $error = "Invalid code. $left attempt(s) remaining.";
            } else {
                // OTP correct — complete login
                $user = $pending['user'];
                unset($_SESSION['2fa_pending']);
                $this->completeLogin($user);
            }
        }

        $maskedEmail = $this->maskEmail($pending['user']['email'] ?? '');
        return ['error' => $error, 'info' => $info, 'masked_email' => $maskedEmail];
    }

    // ------------------------------------------------------------------
    // RESEND 2FA OTP (GET, no body — session guards it)
    // ------------------------------------------------------------------
    public function resend2fa() {
        if (empty($_SESSION['2fa_pending'])) {
            $this->redirect("?url=login");
        }
        $user = $_SESSION['2fa_pending']['user'];
        $this->generateAndSendOtp($user);
        $_SESSION['2fa_info'] = "A new code has been sent to your email.";
        $this->redirect("?url=verify-2fa");
    }

    // ------------------------------------------------------------------
    // GOOGLE OAUTH — step 1: redirect to Google
    // ------------------------------------------------------------------
    public function googleRedirect() {
        $provider = $this->getGoogleProvider();
        $authUrl  = $provider->getAuthorizationUrl([
            'scope'  => ['openid', 'profile', 'email'],
            'prompt' => 'select_account', // always show account picker — prevents auto re-login after logout
        ]);
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: ' . $authUrl);
        exit;
    }

    // ------------------------------------------------------------------
    // GOOGLE OAUTH — step 2: handle Google's callback
    // ------------------------------------------------------------------
    public function googleCallback() {
        // Validate OAuth state to prevent CSRF
        if (
            empty($_GET['state']) ||
            empty($_SESSION['oauth2state']) ||
            !hash_equals($_SESSION['oauth2state'], $_GET['state'])
        ) {
            unset($_SESSION['oauth2state']);
            $_SESSION['login_error'] = "Authentication failed: invalid state. Please try again.";
            $this->redirect("?url=login");
        }
        unset($_SESSION['oauth2state']);

        if (!empty($_GET['error'])) {
            $_SESSION['login_error'] = "Google sign-in was cancelled or denied.";
            $this->redirect("?url=login");
        }

        if (empty($_GET['code'])) {
            $_SESSION['login_error'] = "Authentication failed: missing authorisation code.";
            $this->redirect("?url=login");
        }

        try {
            $provider  = $this->getGoogleProvider();
            $token     = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
            $ownerData = $provider->getResourceOwner($token)->toArray();

            // Google returns: sub (user ID), email, name, picture
            $email    = $ownerData['email'] ?? '';
            $googleId = $ownerData['sub']   ?? ($ownerData['id'] ?? '');
            $name     = $ownerData['name']  ?? 'Devotee';
            $avatar   = $ownerData['picture'] ?? '';

            if (empty($email)) {
                $_SESSION['login_error'] = "Google sign-in failed: no email address was returned. Ensure your Google account has a verified email.";
                $this->redirect("?url=login");
            }

            $user = $this->userModel->findOrCreateGoogleUser([
                'google_id' => $googleId,
                'name'      => $name,
                'email'     => $email,
                'avatar'    => $avatar,
            ]);

            if (!$user) {
                $_SESSION['login_error'] = "Failed to sign in with Google. Please try again.";
                $this->redirect("?url=login");
            }

            // New Google user — collect additional details before pending approval
            if (!empty($user['_is_new'])) {
                $_SESSION['google_onboarding'] = [
                    'user_id'       => $user['id'],
                    'name'          => $user['name'],
                    'email'         => $user['email'],
                    'google_avatar' => $user['google_avatar'] ?? '',
                    'role'          => $user['role'],
                ];
                $this->redirect("?url=google-complete-profile");
            }

            if ($user['approval_status'] !== 'approved') {
                $_SESSION['login_error'] = "Your account is pending admin approval. Please wait for confirmation.";
                $this->redirect("?url=login");
            }

            // Approved Google users bypass 2FA (Google already authenticated them securely)
            $this->completeLogin($user);

        } catch (\Exception $e) {
            error_log("Google OAuth error: " . $e->getMessage());
            $_SESSION['login_error'] = "Google sign-in failed. Please try again or use email/password.";
            $this->redirect("?url=login");
        }
    }

    // ------------------------------------------------------------------
    // GOOGLE ONBOARDING — collect phone + confirm name after first Google login
    // ------------------------------------------------------------------
    public function googleCompleteProfile() {
        // Guard: must have an active onboarding session
        if (empty($_SESSION['google_onboarding'])) {
            $this->redirect("?url=login");
        }

        $ob    = $_SESSION['google_onboarding'];
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name  = trim($_POST['name']  ?? '');
            $phone = trim($_POST['phone'] ?? '');

            if (empty($name)) {
                $error = 'Full name is required.';
            } elseif (empty($phone)) {
                $error = 'Phone number is required.';
            } elseif (!$this->userModel->validatePhone($phone)) {
                $error = 'Please enter a valid 10-digit phone number.';
            } else {
                $this->userModel->saveGoogleOnboarding((int) $ob['user_id'], $name, $phone);

                // Notify all management users about the new pending registration
                $this->userModel->notifyManagementForApproval([
                    'id'    => $ob['user_id'],
                    'name'  => $name,
                    'email' => $ob['email'],
                    'role'  => $ob['role'],
                ]);

                unset($_SESSION['google_onboarding']);
                $_SESSION['login_error'] = "&#10003; Registration complete! Your account is pending admin approval. You'll be able to log in once approved.";
                session_write_close();
                $this->redirect("?url=login");
            }
        }

        return ['ob' => $ob, 'error' => $error];
    }

    // ------------------------------------------------------------------
    // REGISTER
    // ------------------------------------------------------------------
    public function register() {
        $message     = "";
        $messageType = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name            = trim($_POST['name'] ?? '');
            $email           = trim($_POST['email'] ?? '');
            $password        = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role            = $_POST['role'] ?? 'devotee';
            $phone           = trim($_POST['phone'] ?? '');

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
                    $_SESSION['registration_success'] = true;
                    $_SESSION['registration_message'] = $result['message'];
                    $this->redirect("?url=login");
                } else {
                    $message     = $result['message'];
                    $messageType = "error";
                }
            }
        }

        if (isset($_SESSION['registration_success'])) {
            $message     = $_SESSION['registration_message'] ?? 'Registration successful!';
            $messageType = 'success';
            unset($_SESSION['registration_success'], $_SESSION['registration_message']);
        }

        return ['message' => $message, 'messageType' => $messageType];
    }

    // ------------------------------------------------------------------
    // LOGOUT
    // ------------------------------------------------------------------
    public function logout() {
        if (function_exists('destroySession')) {
            destroySession();
        } else {
            $_SESSION = [];
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
            session_destroy();
        }
        header('Clear-Site-Data: "cache"');
        $this->redirect("?url=login");
    }

    // ------------------------------------------------------------------
    // FORGOT PASSWORD
    // ------------------------------------------------------------------
    public function forgotPassword() {
        $message     = "";
        $messageType = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $reset = $this->userModel->createPasswordResetToken($email, 30);
                if (!empty($reset['success'])) {
                    $resetUrl = buildAppUrl("?url=reset-password&token=" . urlencode($reset['token']));
                    $subject  = "Password Reset Request";
                    $body     = "Hello {$reset['user']['name']},\n\nA password reset was requested for your account.\n\nReset Link:\n{$resetUrl}\n\nThis link expires in 30 minutes.\nIf you did not request this, you can ignore this email.";
                    sendEmailNotification($reset['user']['email'], $subject, $body, 'password_reset');
                }
            }
            $message     = "If your email is registered, a password reset link has been sent.";
            $messageType = "success";
        }

        return ['message' => $message, 'messageType' => $messageType];
    }

    // ------------------------------------------------------------------
    // RESET PASSWORD
    // ------------------------------------------------------------------
    public function resetPassword() {
        $error = "";
        $data  = null;

        $token = trim($_GET['token'] ?? $_POST['token'] ?? '');
        if ($token === '') {
            return ['error' => "Reset token is missing.", 'data' => null];
        }

        $tokenData = $this->userModel->getValidPasswordReset($token);
        if (!$tokenData) {
            return ['error' => "This reset link is invalid or expired.", 'data' => null];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword     = $_POST['password'] ?? '';
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

    // ------------------------------------------------------------------
    // PRIVATE HELPERS
    // ------------------------------------------------------------------

    private function completeLogin(array $user): void {
        // Set all session data first, then regenerate (avoids data loss on Windows)
        $_SESSION['user']          = $user;
        $_SESSION['login_time']    = time();
        $_SESSION['last_activity'] = time();
        unset($_SESSION['csrf_token']);
        $_SESSION['user_role'] = $user['role'];

        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(false); // false = keep old session file (safer on Windows)

        error_log("Login completed for {$user['email']}. Role: {$user['role']}");

        // Write session to disk before redirect so the next request can read it
        session_write_close();
        $this->redirect("?url=dashboard");
    }

    private function generateAndSendOtp(array $user): void {
        $otp = sprintf('%06d', random_int(0, 999999));
        $_SESSION['2fa_pending'] = [
            'user'       => $user,
            'otp_hash'   => hash('sha256', $otp),
            'expires_at' => time() + 600, // 10 minutes
            'attempts'   => 0,
        ];
        $subject = "Your " . APP_NAME . " Login Code";
        $body    = "Hello {$user['name']},\n\nYour verification code is:\n\n    {$otp}\n\nThis code expires in 10 minutes.\n\nIf you did not attempt to log in, please ignore this email.";
        sendEmailNotification($user['email'], $subject, $body, '2fa_otp');
    }

    private function getGoogleProvider(): \League\OAuth2\Client\Provider\Google {
        return new \League\OAuth2\Client\Provider\Google([
            'clientId'     => defined('GOOGLE_CLIENT_ID')     ? GOOGLE_CLIENT_ID     : '',
            'clientSecret' => defined('GOOGLE_CLIENT_SECRET') ? GOOGLE_CLIENT_SECRET : '',
            'redirectUri'  => defined('GOOGLE_REDIRECT_URI')  ? GOOGLE_REDIRECT_URI  : '',
            'accessType'   => 'online',
        ]);
    }

    private function maskEmail(string $email): string {
        $parts  = explode('@', $email, 2);
        $local  = $parts[0] ?? '';
        $domain = $parts[1] ?? '';
        $len    = strlen($local);
        if ($len <= 2) {
            return str_repeat('*', $len) . '@' . $domain;
        }
        return substr($local, 0, 1) . str_repeat('*', $len - 2) . substr($local, -1) . '@' . $domain;
    }
}
