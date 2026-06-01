<?php

require_once __DIR__ . "/../models/PoojaRequest.php";
require_once __DIR__ . "/Controller.php";

class PoojaRequestController extends Controller {
    private $poojaRequestModel;

    public function __construct() {
        parent::__construct();
        $this->poojaRequestModel = new PoojaRequest();
    }

    /**
     * Store new pooja request
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];
            $poojaName = trim($_POST['pooja_name'] ?? '');
            $preferredDate = $_POST['preferred_date'] ?? '';
            $preferredTimeSlot = $_POST['preferred_time_slot'] ?? '';
            $specialRequests = trim($_POST['special_requests'] ?? '');

            $minDate = date('Y-m-d', strtotime('+3 days'));
            if (empty($poojaName) || empty($preferredDate)) {
                $_SESSION['error'] = "Pooja name and preferred date are required";
            } elseif ($preferredDate < $minDate) {
                $_SESSION['error'] = "Preferred date must be at least 3 days from today (earliest: " . date('M j, Y', strtotime($minDate)) . ").";
            } else {
                $result = $this->poojaRequestModel->create($userId, $poojaName, $preferredDate, $preferredTimeSlot, $specialRequests);
                if ($result['success']) {
                    $_SESSION['success'] = "Pooja request submitted successfully! We will review and get back to you.";
                } else {
                    $_SESSION['error'] = $result['message'];
                }
            }
        }
        
        $this->redirect("?url=schedule");
    }

    /**
     * Show user's pooja requests
     */
    public function myRequests() {
        if (isset($_SESSION['user'])) {
            $data = $this->poojaRequestModel->getByUserId($_SESSION['user']['id']);
        }
        return ['data' => $data];
    }

    /**
     * Manage all pooja requests (admin)
     */
    public function manage() {
        $this->checkRole('management');
        $data = $this->poojaRequestModel->getAll();
        return ['data' => $data];
    }

    /**
     * Update pooja request status (admin)
     */
    public function updateStatus() {
        $this->checkRole('management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestId = intval($_POST['request_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            $adminRemarks = trim($_POST['admin_remarks'] ?? '');

            if ($requestId > 0 && in_array($status, ['pending', 'approved', 'rejected', 'scheduled'])) {
                $request = $this->poojaRequestModel->getById($requestId);
                if ($request) {
                    $currentStatus = $request['status'];
                    $ok = $this->poojaRequestModel->updateStatus($requestId, $status, $adminRemarks);
                    
                    if ($ok && in_array($status, ['approved', 'scheduled']) && !in_array($currentStatus, ['approved', 'scheduled'])) {
                        // Automatically create a schedule and confirmed booking for the devotee
                        require_once __DIR__ . "/../models/Schedule.php";
                        require_once __DIR__ . "/../models/Booking.php";
                        
                        $scheduleModel = new Schedule();
                        $bookingModel = new Booking();
                        
                        $poojaName = $request['pooja_name'];
                        $poojaDate = $request['preferred_date'];
                        $timeSlot = $request['preferred_time_slot'] ?: '09:00:00';
                        $description = "Pooja request #" . $requestId . ". Special Requests: " . ($request['special_requests'] ?: 'None');
                        
                        $scheduleId = $scheduleModel->create($poojaName, $poojaDate, $timeSlot, $description);
                        if ($scheduleId) {
                            $userId = $request['user_id'];
                            $phone = $request['user_phone'] ?: '';
                            $specialRequests = $request['special_requests'] ?: '';
                            // Use 'email' preference when no phone so phone validation doesn't block the booking.
                            // Booking::create() handles marking the schedule as 'booked' — don't pre-mark here.
                            $notifPref = !empty($phone) ? 'both' : 'email';
                            $bookingModel->create($scheduleId, $userId, $phone, $specialRequests, $notifPref);
                        }
                    }
                    $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Request status updated successfully!" : "Failed to update request status";
                } else {
                    $_SESSION['error'] = "Request not found";
                }
            } else {
                $_SESSION['error'] = "Invalid request";
            }
        }
        
        $this->redirect("?url=pooja-request&action=manage");
    }

    /**
     * Delete pooja request (admin)
     */
    public function delete() {
        $this->checkRole('management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestId = intval($_POST['request_id'] ?? 0);
            $ok = $requestId > 0 ? $this->poojaRequestModel->delete($requestId) : false;
            $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Request deleted successfully!" : "Failed to delete request";
        }
        
        $this->redirect("?url=pooja-request&action=manage");
    }
}
