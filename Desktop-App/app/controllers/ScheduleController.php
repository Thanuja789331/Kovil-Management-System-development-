<?php

require_once __DIR__ . "/../models/Schedule.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Booking.php";
require_once __DIR__ . "/Controller.php";

class ScheduleController extends Controller {
    private $scheduleModel;
    private $userModel;
    private $bookingModel;

    public function __construct() {
        parent::__construct();
        $this->scheduleModel = new Schedule();
        $this->userModel = new User();
        $this->bookingModel = new Booking();
    }

    /**
     * Display all schedules
     */
    public function index() {
        $filterDate = trim($_GET['filter_date'] ?? '');
        $filterType = trim($_GET['pooja_type'] ?? '');
        $isValidFilterDate = $filterDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate);

        $data = $this->scheduleModel->getAllWithBookingsFiltered(
            $isValidFilterDate ? $filterDate : null,
            $filterType !== '' ? $filterType : null,
            $isValidFilterDate ? null : date('Y-m-d')   // default: only today-and-future
        );

        return ['data' => $data];
    }

    /**
     * Show add schedule form
     */
    public function add() {
        $this->checkRole('management');
        $view = __DIR__ . "/../views/schedule/add.php";
        $this->loadView($view);
    }

    /**
     * Store new schedule
     */
    public function store() {
        $this->checkRole('management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pooja_name = trim($_POST['pooja_name'] ?? '');
            $pooja_date = $_POST['pooja_date'] ?? '';
            $time_slot = $_POST['time_slot'] ?? '';
            $description = trim($_POST['description'] ?? '');

            if (empty($pooja_name) || empty($pooja_date) || empty($time_slot)) {
                $_SESSION['error'] = "Please fill in all required fields";
            } else {
                $result = $this->scheduleModel->create($pooja_name, $pooja_date, $time_slot, $description);
                
                if ($result) {
                    $_SESSION['success'] = "Pooja scheduled successfully!";
                } else {
                    $_SESSION['error'] = "Failed to schedule pooja";
                }
            }
        }
        
        $this->redirect("?url=schedule&action=add");
    }

    /**
     * Show edit schedule form
     */
    public function edit() {
        $this->checkRole('management');
        
        $scheduleId = intval($_GET['id'] ?? 0);
        if ($scheduleId > 0) {
            $scheduleData = $this->scheduleModel->getByIdWithBooking($scheduleId);
            $approvedDevotees = $this->userModel->getApprovedDevotees();
            if (!$scheduleData) {
                $_SESSION['error'] = "Schedule not found";
                $this->redirect("?url=schedule");
            }
            $data = [
                'schedule' => $scheduleData,
                'approvedDevotees' => $approvedDevotees
            ];
        } else {
            $this->redirect("?url=schedule");
        }

        $view = __DIR__ . "/../views/schedule/edit.php";
        $this->loadView($view, $data);
    }

    /**
     * Update schedule
     */
    public function update() {
        $this->checkRole('management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $scheduleId = intval($_POST['id'] ?? 0);
            $pooja_name = trim($_POST['pooja_name'] ?? '');
            $pooja_date = $_POST['pooja_date'] ?? '';
            $time_slot = $_POST['time_slot'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $bookingStatus = trim($_POST['booking_status'] ?? '');
            $assignedDevoteeId = intval($_POST['assigned_devotee_id'] ?? 0);

            if (empty($pooja_name) || empty($pooja_date) || empty($time_slot) || $scheduleId <= 0) {
                $_SESSION['error'] = "Please fill in all required fields";
            } else {
                $result = $this->scheduleModel->update($scheduleId, $pooja_name, $pooja_date, $time_slot, $description);
                if ($result && in_array($bookingStatus, ['booked', 'available'], true)) {
                    $bookingUpdate = $this->bookingModel->adminManageScheduleBooking($scheduleId, $bookingStatus, $assignedDevoteeId > 0 ? $assignedDevoteeId : null);
                    if (!$bookingUpdate['success']) {
                        $result = false;
                        $_SESSION['error'] = $bookingUpdate['message'];
                    }
                }
                
                if ($result) {
                    $_SESSION['success'] = "Pooja updated successfully!";
                } else {
                    if (!isset($_SESSION['error'])) {
                        $_SESSION['error'] = "Failed to update pooja";
                    }
                }
            }
            $this->redirect("?url=schedule&action=edit&id=" . $scheduleId);
        }
    }

    /**
     * Delete schedule
     */
    public function delete() {
        $this->checkRole('management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $scheduleId = intval($_POST['id'] ?? 0);
            if ($scheduleId > 0) {
                $result = $this->scheduleModel->delete($scheduleId);
                if ($result) {
                    $_SESSION['success'] = "Pooja deleted successfully!";
                } else {
                    $_SESSION['error'] = "Failed to delete pooja";
                }
            } else {
                $_SESSION['error'] = "Invalid schedule ID";
            }
        }
        
        $this->redirect("?url=schedule");
    }
}
