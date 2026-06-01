<?php

require_once __DIR__ . "/../models/Schedule.php";
require_once __DIR__ . "/../models/Booking.php";
require_once __DIR__ . "/Controller.php";

class BookingController extends Controller {
    private $scheduleModel;
    private $bookingModel;

    public function __construct() {
        parent::__construct();
        $this->scheduleModel = new Schedule();
        $this->bookingModel = new Booking();
    }

    /**
     * Handle booking form
     */
    public function book() {
        $error = "";
        $data = null;
        $scheduleId = intval($_GET['id'] ?? 0);
        
        // Debug logging
        error_log("Booking - Schedule ID: " . $scheduleId);
        error_log("Booking - User ID: " . ($_SESSION['user']['id'] ?? 'NOT SET'));
        
        // Handle POST request for booking (non-AJAX fallback)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['action']) && isset($_SESSION['user'])) {
            $devoteePhone = trim($_POST['phone'] ?? '');
            $specialRequests = trim($_POST['special_requests'] ?? '');
            $notificationPreference = trim($_POST['notification_preference'] ?? 'both');

            if (empty($_SESSION['user']['id'])) {
                $error = "User not logged in";
                error_log("Booking Error: User not logged in");
            } else {
                $bookingData = [
                    'schedule_id' => $scheduleId,
                    'user_id' => (int) $_SESSION['user']['id'],
                    'phone' => $devoteePhone,
                    'special_requests' => $specialRequests,
                    'notification_preference' => $notificationPreference,
                ];

                $errors = $this->bookingModel->validateBooking($bookingData);
                
                if (empty($errors)) {
                    $result = $this->bookingModel->create($scheduleId, $_SESSION['user']['id'], $devoteePhone, $specialRequests, $notificationPreference);
                    
                    if ($result['success']) {
                        error_log("Booking Success - Booking ID: " . $result['booking_id']);
                        $this->redirect("?url=confirmation&booking_id=" . $result['booking_id']);
                    } else {
                        $error = $result['message'];
                        error_log("Booking Failed: " . $error);
                    }
                } else {
                    $error = implode(", ", $errors);
                    error_log("Booking Validation Errors: " . $error);
                }
            }
        } elseif (!isset($_SESSION['user'])) {
            $error = "Please login to book a pooja";
        }
        
        // Display booking form if no POST or validation failed
        if ($scheduleId > 0 && isset($_SESSION['user']) && empty($error)) {
            if ($this->scheduleModel->isAvailable($scheduleId)) {
                $data = $this->scheduleModel->getById($scheduleId);
                if ($data && $data['pooja_date'] < date('Y-m-d')) {
                    $error = "This pooja date has already passed and cannot be booked";
                    $data = null;
                }
            } else {
                $error = "This pooja is no longer available for booking";
            }
        } elseif (empty($error)) {
            $error = "Invalid schedule selected";
        }
        
        if (!empty($error) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("?url=schedule&error=" . urlencode($error));
        }

        return ['error' => $error, 'data' => $data];
    }

    /**
     * Handle confirmation page
     */
    public function confirmation() {
        $action = $_GET['action'] ?? '';

        if ($action === 'download-pdf') {
            $bookingId = intval($_GET['booking_id'] ?? 0);
            if ($bookingId <= 0) {
                $this->redirect("?url=schedule");
            }

            $bookingData = $this->bookingModel->getById($bookingId);
            if (!$bookingData) {
                $_SESSION['error'] = "Booking not found";
                $this->redirect("?url=schedule");
            }

            $currentUser = $_SESSION['user'] ?? null;
            $canDownload = $currentUser && (
                ($currentUser['role'] ?? '') === 'management' ||
                (int) ($bookingData['user_id'] ?? 0) === (int) ($currentUser['id'] ?? 0)
            );
            if (!$canDownload) {
                $_SESSION['error'] = "You are not authorized to download this booking receipt.";
                $this->redirect("?url=my-bookings");
            }

            require_once __DIR__ . '/../../config/pdf_helper.php';
            $bringItems = $this->bookingModel->getBringItemsForBooking($bookingData);
            $filename = 'pooja_receipt_' . preg_replace('/[^A-Za-z0-9_-]/', '_', (string) ($bookingData['booking_reference'] ?? $bookingId)) . '.pdf';
            outputSimpleBookingPdf($bookingData, $bringItems, $filename);
            exit;
        }

        // Handle SMS sending request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
            header('Content-Type: application/json');
            
            $bookingId = intval($_POST['booking_id']);
            $channel = strtolower(trim($_POST['notification_channel'] ?? 'both'));
            
            try {
                // Get booking details
                $bookingData = $this->bookingModel->getById($bookingId);
                
                if (!$bookingData) {
                    echo json_encode(['success' => false, 'message' => 'Booking not found']);
                    exit;
                }
                
                $result = $this->bookingModel->sendBookingNotification($bookingId, $channel);
                
                echo json_encode([
                    'success' => $result['success'],
                    'message' => $result['message']
                ]);
                exit;
                
            } catch (Exception $e) {
                error_log("Send SMS Error: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Failed to send SMS. Please try again.']);
                exit;
            }
        }
        
        // Show confirmation page
        $bookingId = intval($_GET['booking_id'] ?? 0);
        if ($bookingId > 0) {
            $data = $this->bookingModel->getById($bookingId);
            if (!$data) {
                $_SESSION['error'] = "Booking not found";
                $this->redirect("?url=schedule");
            }
        } else {
            $this->redirect("?url=schedule");
        }

        return ['data' => $data];
    }

    /**
     * Handle my bookings
     */
    public function myBookings() {
        $userId = (int) $_SESSION['user']['id'];
        $data   = $this->bookingModel->getByUser($userId);

        require_once __DIR__ . '/../models/PoojaRequest.php';
        $poojaRequestModel = new PoojaRequest();
        $reqResult   = $poojaRequestModel->getByUserId($userId);
        $poojaRequests = [];
        if ($reqResult) {
            while ($row = $reqResult->fetch_assoc()) {
                $poojaRequests[] = $row;
            }
        }

        return ['data' => $data, 'poojaRequests' => $poojaRequests];
    }

    /**
     * Management: search bookings by name, phone, or reference
     */
    public function search() {
        $query = trim($_GET['q'] ?? '');
        $results = !empty($query) ? $this->bookingModel->searchBookings($query) : [];
        return ['results' => $results, 'query' => $query];
    }

    /**
     * Cancel booking action
     */
    public function cancel() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = intval($_POST['id'] ?? 0);
            if ($bookingId > 0 && isset($_SESSION['user'])) {
                $booking = $this->bookingModel->getById($bookingId);
                if ($booking) {
                    $isManagement = $_SESSION['user']['role'] === 'management';
                    $isOwner = (int)$booking['user_id'] === (int)$_SESSION['user']['id'];

                    if (!$isManagement && !$isOwner) {
                        $_SESSION['error'] = "Access denied. You do not have permission to cancel this booking.";
                    } elseif ($booking['pooja_date'] < date('Y-m-d')) {
                        $_SESSION['error'] = "This pooja has already taken place and cannot be cancelled.";
                    } elseif ($booking['status'] === 'cancelled') {
                        $_SESSION['error'] = "This booking has already been cancelled.";
                    } else {
                        // Pass null for management so the model skips the ownership check
                        $ownerIdToCheck = $isManagement ? null : (int)$_SESSION['user']['id'];
                        $success = $this->bookingModel->cancel($bookingId, $ownerIdToCheck);
                        if ($success) {
                            $_SESSION['success'] = "Booking cancelled successfully. The slot is now available for other devotees.";
                        } else {
                            $_SESSION['error'] = "Failed to cancel booking. Please try again.";
                        }
                    }
                } else {
                    $_SESSION['error'] = "Booking not found.";
                }
            }
        }
        $this->redirect("?url=my-bookings");
    }
}
