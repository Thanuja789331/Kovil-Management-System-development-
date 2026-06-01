<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/Controller.php";

class ProfileController extends Controller {
    private User $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(): array {
        $userId  = (int) $_SESSION['user']['id'];
        $profile = $this->userModel->getProfileById($userId);
        $message = '';
        $messageType = '';

        if (!empty($_SESSION['profile_message'])) {
            $message     = $_SESSION['profile_message'];
            $messageType = $_SESSION['profile_message_type'] ?? 'success';
            unset($_SESSION['profile_message'], $_SESSION['profile_message_type']);
        }

        return ['profile' => $profile, 'message' => $message, 'messageType' => $messageType];
    }

    public function update(): void {
        $userId = (int) $_SESSION['user']['id'];
        $name   = trim($_POST['name']  ?? '');
        $email  = trim($_POST['email'] ?? '');
        $phone  = trim($_POST['phone'] ?? '') ?: null;

        if (empty($name) || empty($email)) {
            $this->flashAndRedirect('Name and email are required.', 'error');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashAndRedirect('Please enter a valid email address.', 'error');
        }

        if ($phone !== null && !$this->userModel->validatePhone($phone)) {
            $this->flashAndRedirect('Please enter a valid 10-digit phone number.', 'error');
        }

        $result = $this->userModel->updateProfile($userId, $name, $email, $phone);

        if ($result['success']) {
            // Keep session in sync with the updated values
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;
            if ($phone !== null) {
                $_SESSION['user']['phone'] = $phone;
            }
            $this->flashAndRedirect('Profile updated successfully.', 'success');
        }

        $this->flashAndRedirect($result['message'], 'error');
    }

    public function toggle2fa(): void {
        $userId = (int) $_SESSION['user']['id'];
        $enable = ($_POST['action'] ?? '') === 'enable';
        $result = $this->userModel->toggle2FA($userId, $enable);

        if ($result['success']) {
            $msg = $enable
                ? 'Two-factor authentication enabled. You will receive a verification code by email on each login.'
                : 'Two-factor authentication disabled.';
            $this->flashAndRedirect($msg, 'success');
        }

        $this->flashAndRedirect('Failed to update 2FA setting. Please try again.', 'error');
    }

    public function changePassword(): void {
        $userId     = (int) $_SESSION['user']['id'];
        $hasPassword = !empty($_POST['has_password']);
        $newPw      = $_POST['new_password']     ?? '';
        $confirmPw  = $_POST['confirm_password'] ?? '';

        if (strlen($newPw) < 6) {
            $this->flashAndRedirect('Password must be at least 6 characters.', 'error');
        }

        if ($newPw !== $confirmPw) {
            $this->flashAndRedirect('Passwords do not match.', 'error');
        }

        if ($hasPassword) {
            $currentPw = $_POST['current_password'] ?? '';
            if (empty($currentPw)) {
                $this->flashAndRedirect('Please enter your current password.', 'error');
            }
            $result = $this->userModel->changePassword($userId, $currentPw, $newPw);
        } else {
            $result = $this->userModel->setPassword($userId, $newPw);
        }

        $this->flashAndRedirect($result['message'], $result['success'] ? 'success' : 'error');
    }

    public function uploadAvatar(): void {
        $userId = (int) $_SESSION['user']['id'];

        // Validate upload error
        if (empty($_FILES['avatar']['tmp_name'])) {
            $this->flashAndRedirect('No image was uploaded.', 'error');
        }
        
        if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = $this->getUploadErrorMessage($_FILES['avatar']['error']);
            $this->flashAndRedirect($errorMsg, 'error');
        }

        $file = $_FILES['avatar'];

        if ($file['size'] > 5 * 1024 * 1024) {
            $this->flashAndRedirect('Image must be under 5 MB.', 'error');
        }

        $info = @getimagesize($file['tmp_name']);
        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            $this->flashAndRedirect('Only JPEG, PNG, and WebP images are allowed.', 'error');
        }

        $uploadDir = defined('UPLOAD_PATH') ? UPLOAD_PATH . 'avatars/' : __DIR__ . '/../../public/uploads/avatars/';
        
        // Ensure upload directory exists and is writable
        if (!is_dir($uploadDir)) {
            try {
                if (!mkdir($uploadDir, 0755, true)) {
                    error_log("[ERROR] Failed to create upload directory: " . $uploadDir);
                    $this->flashAndRedirect('Upload directory not writable. Please contact support.', 'error');
                }
            } catch (Throwable $e) {
                error_log("[ERROR] Exception creating upload directory: " . $e->getMessage());
                $this->flashAndRedirect('Upload directory error. Please contact support.', 'error');
            }
        }
        
        if (!is_writable($uploadDir)) {
            error_log("[ERROR] Upload directory not writable: " . $uploadDir);
            $this->flashAndRedirect('Upload directory not writable. Please contact support.', 'error');
        }

        $filename = $userId . '.jpg';
        $destPath = $uploadDir . $filename;

        if (!$this->resizeAndSave($file['tmp_name'], $info[2], $destPath)) {
            error_log("[ERROR] Failed to process image for user: " . $userId);
            $this->flashAndRedirect('Failed to process image. Please try another file.', 'error');
        }

        $result = $this->userModel->updateAvatar($userId, $filename);
        if (!$result['success']) {
            error_log("[ERROR] Failed to save avatar to database for user: " . $userId);
            $this->flashAndRedirect('Failed to save avatar. Please try again.', 'error');
        }

        $_SESSION['user']['avatar'] = $filename;
        $this->flashAndRedirect('Profile photo updated successfully.', 'success');
    }

    /**
     * Get user-friendly message for upload errors
     */
    private function getUploadErrorMessage(int $errorCode): string {
        return match($errorCode) {
            UPLOAD_ERR_INI_SIZE => 'File too large (exceeds server limit).',
            UPLOAD_ERR_FORM_SIZE => 'File too large (exceeds form limit).',
            UPLOAD_ERR_PARTIAL => 'File upload was incomplete. Please try again.',
            UPLOAD_ERR_NO_FILE => 'No file selected.',
            UPLOAD_ERR_NO_TMP_DIR => 'Upload directory unavailable.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file. Disk may be full.',
            UPLOAD_ERR_EXTENSION => 'File type not allowed.',
            default => 'Unknown upload error. Please try again.'
        };
    }

    private function resizeAndSave(string $src, int $type, string $dest, int $size = 200): bool {
        $srcImg = match($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($src),
            IMAGETYPE_PNG  => @imagecreatefrompng($src),
            IMAGETYPE_WEBP => @imagecreatefromwebp($src),
            default        => false,
        };
        if (!$srcImg) return false;

        $w = imagesx($srcImg);
        $h = imagesy($srcImg);

        // Crop to square from center, then resize to $size x $size
        $minSide = min($w, $h);
        $cropX   = (int) (($w - $minSide) / 2);
        $cropY   = (int) (($h - $minSide) / 2);

        $canvas = imagecreatetruecolor($size, $size);
        imagecopyresampled($canvas, $srcImg, 0, 0, $cropX, $cropY, $size, $size, $minSide, $minSide);

        $ok = imagejpeg($canvas, $dest, 85);

        imagedestroy($srcImg);
        imagedestroy($canvas);
        return $ok;
    }

    private function flashAndRedirect(string $message, string $type): void {
        $_SESSION['profile_message']      = $message;
        $_SESSION['profile_message_type'] = $type;
        $this->redirect('?url=profile');
    }
}
