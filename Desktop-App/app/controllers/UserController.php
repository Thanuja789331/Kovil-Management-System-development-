<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Duty.php";
require_once __DIR__ . "/Controller.php";

class UserController extends Controller {
    private $userModel;
    private $dutyModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->dutyModel = new Duty();
    }

    /**
     * Handle registration approval
     */
    public function approveRegistration() {
        $this->checkRole('management');
        
        $message = "";
        $messageType = "";
        
        // Handle approval/rejection
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = intval($_POST['user_id'] ?? 0);
            $action = $_POST['action'] ?? ''; // 'approve' or 'reject'
            $remarks = trim($_POST['remarks'] ?? '');
            
            if ($userId > 0 && in_array($action, ['approve', 'reject'])) {
                $status = $action === 'approve' ? 'approved' : 'rejected';
                $result = $this->userModel->updateApprovalStatus($userId, $status, $_SESSION['user']['id'], $remarks);
                
                if ($result['success']) {
                    $message = $result['message'];
                    $messageType = "success";
                } else {
                    $message = $result['message'];
                    $messageType = "error";
                }
            } else {
                $message = "Invalid request";
                $messageType = "error";
            }
        }
        
        // Get pending registrations
        $data = $this->userModel->getPendingRegistrations();

        return [
            'message' => $message,
            'messageType' => $messageType,
            'data' => $data
        ];
    }

    /**
     * Handle manage users
     */
    public function manageUsers() {
        $this->checkRole('management');

        $message = '';
        $messageType = '';
        if (isset($_SESSION['success'])) {
            $message = $_SESSION['success'];
            $messageType = 'success';
            unset($_SESSION['success']);
        } elseif (isset($_SESSION['error'])) {
            $message = $_SESSION['error'];
            $messageType = 'error';
            unset($_SESSION['error']);
        }

        $result = $this->userModel->getAllUsers();
        $allUsers = [];
        $stats = ['total' => 0, 'devotee' => 0, 'priest' => 0, 'management' => 0];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $allUsers[] = $row;
                $stats['total']++;
                if (isset($stats[$row['role']])) {
                    $stats[$row['role']]++;
                }
            }
        }

        return ['data' => $allUsers, 'stats' => $stats, 'message' => $message, 'messageType' => $messageType];
    }

    /**
     * Update a user's details
     */
    public function updateUser() {
        $this->checkRole('management');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId       = intval($_POST['user_id'] ?? 0);
            $name         = trim($_POST['name'] ?? '');
            $email        = trim($_POST['email'] ?? '');
            $phone        = trim($_POST['phone'] ?? '');
            $role         = $_POST['role'] ?? '';
            $approvalStatus = $_POST['approval_status'] ?? '';

            $validRoles    = ['devotee', 'priest', 'management'];
            $validStatuses = ['approved', 'pending', 'rejected'];

            if ($userId <= 0 || empty($name) || empty($email) || !in_array($role, $validRoles) || !in_array($approvalStatus, $validStatuses)) {
                $_SESSION['error'] = 'Invalid input data. All fields are required.';
            } else {
                $result = $this->userModel->updateUser($userId, $name, $email, $phone ?: null, $role, $approvalStatus);
                $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
            }
        }
        $this->redirect('?url=manage-users');
    }

    /**
     * Delete a user
     */
    public function deleteUser() {
        $this->checkRole('management');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = intval($_POST['user_id'] ?? 0);
            if ($userId === intval($_SESSION['user']['id'])) {
                $_SESSION['error'] = 'You cannot delete your own account';
            } elseif ($userId > 0) {
                $ok = $this->userModel->deleteUser($userId);
                $_SESSION[$ok ? 'success' : 'error'] = $ok ? 'User deleted successfully' : 'Failed to delete user';
            } else {
                $_SESSION['error'] = 'Invalid user ID';
            }
        }
        $this->redirect('?url=manage-users');
    }

    /**
     * Handle priest duties view
     */
    public function priest() {
        $this->checkRole('priest');
        $data = $this->dutyModel->getByPriest($_SESSION['user']['id']);
        return ['data' => $data];
    }

    /**
     * Handle priest schedules view
     */
    public function priestSchedules() {
        // Priest can view all schedules and festivals
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['priest', 'management'])) {
            $this->redirect("?url=login");
        }
        
        require_once __DIR__ . "/../models/Festival.php";
        $festivalModel = new Festival();
        
        $schedules = $this->dutyModel->getAllSchedulesWithAssignments();
        $festivals = $festivalModel->getAll();

        return ['schedules' => $schedules, 'festivals' => $festivals];
    }
}
