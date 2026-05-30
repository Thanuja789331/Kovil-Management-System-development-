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
        
        // Get all users
        $data = $this->userModel->getAllUsers();
        
        // Calculate statistics
        $stats = [
            'total' => 0,
            'devotee' => 0,
            'priest' => 0,
            'management' => 0
        ];
        
        if ($data && $data->num_rows > 0) {
            $tempData = $data;
            $tempData->data_seek(0);
            while ($row = $tempData->fetch_assoc()) {
                $stats['total']++;
                if (isset($stats[$row['role']])) {
                    $stats[$row['role']]++;
                }
            }
        }

        return ['data' => $data, 'stats' => $stats];
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
