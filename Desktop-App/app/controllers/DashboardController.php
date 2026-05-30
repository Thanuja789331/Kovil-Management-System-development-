<?php

require_once __DIR__ . "/../models/Schedule.php";
require_once __DIR__ . "/../models/Booking.php";
require_once __DIR__ . "/../models/Duty.php";
require_once __DIR__ . "/../models/Donation.php";
require_once __DIR__ . "/Controller.php";

class DashboardController extends Controller {
    private $scheduleModel;
    private $bookingModel;
    private $dutyModel;
    private $donationModel;

    public function __construct() {
        parent::__construct();
        $this->scheduleModel = new Schedule();
        $this->bookingModel = new Booking();
        $this->dutyModel = new Duty();
        $this->donationModel = new Donation();
    }

    /**
     * Handle dashboard based on user role
     */
    public function index() {
        $data = null;
        $poojas = 0;
        $donations = 0;
        $bookings = 0;

        $userRole = $_SESSION['user']['role'] ?? '';
        
        // Debug: Log dashboard access (remove in production)
        error_log("Dashboard access. User role: $userRole, Full user: " . json_encode($_SESSION['user']));
        
        if ($userRole === 'management') {
            // Admin dashboard with statistics
            $poojas = $this->scheduleModel->getTotalCount();
            $donations = $this->donationModel->getTotalAmount();
            $bookings = $this->bookingModel->getTotalBookings();
        } elseif ($userRole === 'priest') {
            // Priest dashboard - show assigned duties, schedules, and festivals
            $priestId = $_SESSION['user']['id'];
            $data = $this->dutyModel->getByPriest($priestId);
        } elseif ($userRole === 'devotee') {
            // Devotee dashboard - show recent bookings and upcoming poojas
            $data = $this->bookingModel->getByUser($_SESSION['user']['id']);
        }

        return [
            'data' => $data,
            'poojas' => $poojas,
            'donations' => $donations,
            'bookings' => $bookings
        ];
    }
}
