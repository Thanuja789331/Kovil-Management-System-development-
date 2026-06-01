<?php

require_once __DIR__ . "/../models/Duty.php";
require_once __DIR__ . "/../models/Schedule.php";
require_once __DIR__ . "/Controller.php";

class AssignController extends Controller {
    private $dutyModel;
    private $scheduleModel;

    public function __construct() {
        parent::__construct();
        $this->dutyModel = new Duty();
        $this->scheduleModel = new Schedule();
    }

    /**
     * Handle priest assignment page
     */
    public function index() {
        $this->checkRole('management');

        $message = "";
        $messageType = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $priestId = intval($_POST['priest'] ?? 0);
            $scheduleId = intval($_POST['schedule'] ?? 0);

            // Validate both values are selected
            if ($priestId <= 0 || $scheduleId <= 0) {
                $message = "Please select both priest and pooja schedule";
                $messageType = "error";
            } else {
                if ($this->dutyModel->isAlreadyAssigned($priestId, $scheduleId)) {
                    $message = "This priest is already assigned to this pooja";
                    $messageType = "error";
                } else {
                    // Check priest availability
                    $schedule = $this->scheduleModel->getById($scheduleId);
                    if ($schedule && $this->dutyModel->isPriestAvailable($priestId, $schedule['pooja_date'])) {
                        $result = $this->dutyModel->assign($priestId, $scheduleId);
                        
                        if ($result['success']) {
                            $message = "Priest assigned successfully!";
                            $messageType = "success";
                        } else {
                            $message = $result['message'];
                            $messageType = "error";
                        }
                    } else {
                        $message = "Priest is not available on this date (maximum 3 assignments per day)";
                        $messageType = "error";
                    }
                }
            }
        }

        // Get selected date for availability check (default to today)
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        
        // Get priests with their availability
        $priests = $this->dutyModel->getPriestAvailability($selectedDate);
        
        // Get all pooja schedules for priest assignment (including past dates for reference)
        // Order by date descending to show most recent first
        $schedules = $this->scheduleModel->getAllChronological();

        $duties = $this->dutyModel->getAllDetailed();

        return [
            'message' => $message,
            'messageType' => $messageType,
            'priests' => $priests,
            'schedules' => $schedules,
            'selectedDate' => $selectedDate,
            'duties' => $duties
        ];
    }
}
