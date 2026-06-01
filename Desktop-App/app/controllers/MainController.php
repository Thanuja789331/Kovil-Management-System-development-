<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Schedule.php";
require_once __DIR__ . "/../models/Booking.php";
require_once __DIR__ . "/../models/Duty.php";
require_once __DIR__ . "/../models/Donation.php";
require_once __DIR__ . "/../models/Announcement.php";
require_once __DIR__ . "/../models/Festival.php";
require_once __DIR__ . "/../models/SpecialDay.php";
require_once __DIR__ . "/../models/PoojaRequest.php";

class MainController {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function load($page = 'login') {
        // Initialize models
        $userModel = new User();
        $scheduleModel = new Schedule();
        $bookingModel = new Booking();
        $dutyModel = new Duty();
        $donationModel = new Donation();
        $announcementModel = new Announcement();
        $festivalModel = new Festival();
        $specialDayModel = new SpecialDay();
        $poojaRequestModel = new PoojaRequest();

        // Lightweight scheduler: process due email reminders during normal traffic.
        if (function_exists('sendEmailNotification')) {
            $bookingModel->processUpcomingEmailReminders();
        }

        // ===========================================
        // AUTHENTICATION CHECK
        // Session is already initialized in bootstrap.php
        // ===========================================
        $publicPages = ['login', 'register', 'logout', 'home', 'forgot-password', 'reset-password'];
        
        if (!in_array($page, $publicPages)) {
            // Check if user is logged in
            if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
                header("Location: ?url=login");
                exit;
            }
        }

        // Centralized RBAC guard for admin-only pages/actions.
        if (isset($_SESSION['user'])) {
            $currentRole = $_SESSION['user']['role'] ?? '';
            $actionParam = $_GET['action'] ?? '';
            $adminOnlyPages = [
                'manage-users',
                'approve-registration',
                'assign',
                'report',
                'export-poojas',
                'export-donations-pdf'
            ];
            $adminOnlyActionsByPage = [
                'schedule' => ['add', 'store', 'edit', 'update', 'delete'],
                'festival' => ['add', 'store', 'edit', 'update', 'delete'],
                'announcement' => ['create', 'store']
            ];

            $isAdminOnlyAction = isset($adminOnlyActionsByPage[$page]) && in_array($actionParam, $adminOnlyActionsByPage[$page], true);
            $isAdminOnlyPage = in_array($page, $adminOnlyPages, true);
            if (($isAdminOnlyPage || $isAdminOnlyAction) && $currentRole !== 'management') {
                $_SESSION['error'] = "Access denied. You do not have permission to view that page.";
                header("Location: ?url=dashboard");
                exit;
            }
        }

        // Initialize variables to avoid undefined variable errors
        $message = "";
        $messageType = "";
        $error = "";
        $data = null;
        $poojas = 0;
        $donations = 0;
        $bookings = 0;
        $avgDonation = 0;
        $priests = null;
        $schedules = null;

        switch ($page) {
            case 'home':
                // Public landing page — no data needed
                break;

            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $email = trim($_POST['email'] ?? '');
                    $password = $_POST['password'] ?? '';

                    if (empty($email) || empty($password)) {
                        $error = "Please enter both email and password";
                    } else {
                        $user = $userModel->login($email, $password);

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
                            header("Location: ?url=dashboard");
                            exit;
                        } else {
                            // Check if user exists but is pending approval
                            $checkUser = $userModel->getByEmail($email);
                            if ($checkUser && $checkUser['approval_status'] === 'pending') {
                                $error = "<div>⏳ Your account is pending admin approval. Please wait for approval before logging in.</div><div class='mt-2'><small>Admin will review your registration shortly.</small></div>";
                            } elseif ($checkUser && $checkUser['approval_status'] === 'rejected') {
                                $error = "<div>❌ Your account has been rejected. Please contact admin for more information.</div>";
                            } elseif ($checkUser) {
                                // User exists but wrong password or role mismatch
                                $error = "<div>❌ Invalid password. Please try again.</div>";
                            } else {
                                // User doesn't exist at all
                                $error = "<div>❌ No account found with this email address.</div><div class='mt-2'><small>Please register first or check your email.</small></div>";
                            }
                        }
                    }
                }
                break;

            case 'register':
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
                    } elseif (!empty($phone) && !$userModel->validatePhone($phone)) {
                        $message = "Please enter a valid phone number";
                        $messageType = "error";
                    } else {
                        $result = $userModel->register($name, $email, $password, $role, $phone);
                        
                        if ($result['success']) {
                            // Set success message in session and redirect to login
                            $_SESSION['registration_success'] = true;
                            $_SESSION['registration_message'] = $result['message'];
                            
                            // Redirect to login page
                            header("Location: ?url=login");
                            exit;
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
                break;

            case 'forgot-password':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $email = trim($_POST['email'] ?? '');
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $reset = $userModel->createPasswordResetToken($email, 30);
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
                break;

            case 'reset-password':
                $token = trim($_GET['token'] ?? $_POST['token'] ?? '');
                if ($token === '') {
                    $error = "Reset token is missing.";
                    break;
                }

                $tokenData = $userModel->getValidPasswordReset($token);
                if (!$tokenData) {
                    $error = "This reset link is invalid or expired.";
                    break;
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $newPassword = $_POST['password'] ?? '';
                    $confirmPassword = $_POST['confirm_password'] ?? '';
                    if (strlen($newPassword) < 6) {
                        $error = "Password must be at least 6 characters.";
                    } elseif ($newPassword !== $confirmPassword) {
                        $error = "Passwords do not match.";
                    } else {
                        $result = $userModel->resetPasswordWithToken($token, $newPassword);
                        if ($result['success']) {
                            $_SESSION['registration_success'] = true;
                            $_SESSION['registration_message'] = "Password reset successful. Please login.";
                            header("Location: ?url=login");
                            exit;
                        }
                        $error = $result['message'];
                    }
                }
                $data = ['token' => $token, 'user_email' => $tokenData['email']];
                break;

            case 'approve-registration':
                $this->checkRole('management');
                
                // Handle approval/rejection
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $userId = intval($_POST['user_id'] ?? 0);
                    $action = $_POST['action'] ?? ''; // 'approve' or 'reject'
                    $remarks = trim($_POST['remarks'] ?? '');
                    
                    if ($userId > 0 && in_array($action, ['approve', 'reject'])) {
                        $status = $action === 'approve' ? 'approved' : 'rejected';
                        $result = $userModel->updateApprovalStatus($userId, $status, $_SESSION['user']['id'], $remarks);
                        
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
                $data = $userModel->getPendingRegistrations();
                break;

            case 'manage-users':
                $this->checkRole('management');
                
                // Get all users
                $data = $userModel->getAllUsers();
                
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
                break;

            case 'dashboard':
                // Dashboard is now role-based
                $userRole = $_SESSION['user']['role'] ?? '';
                
                // Debug: Log dashboard access (remove in production)
                error_log("Dashboard access. User role: $userRole, Full user: " . json_encode($_SESSION['user']));
                
                if ($userRole === 'management') {
                    // Admin dashboard with statistics
                    $poojas = $scheduleModel->getTotalCount();
                    $donations = $donationModel->getTotalAmount();
                    $bookings = $bookingModel->getTotalBookings();
                } elseif ($userRole === 'priest') {
                    // Priest dashboard - show assigned duties, schedules, and festivals
                    $priestId = $_SESSION['user']['id'];
                    $data = $dutyModel->getByPriest($priestId);
                } elseif ($userRole === 'devotee') {
                    // Devotee dashboard - show recent bookings and upcoming poojas
                    $data = $bookingModel->getByUser($_SESSION['user']['id']);
                }
                break;

            case 'pooja-history':
                // Pagination
                $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $perPage = 20;
                $offset = ($page - 1) * $perPage;
                
                // Get all poojas with booking information and pagination
                $data = $scheduleModel->getAllWithBookingsPaginated($perPage, $offset);
                $totalPoojas = $scheduleModel->getTotalCount();
                $totalPages = ceil($totalPoojas / $perPage);
                
                // Calculate statistics
                $stats = [
                    'total' => 0,
                    'available' => 0,
                    'booked' => 0,
                    'completed' => 0
                ];
                
                // Get analytics data
                $analytics = [
                    'popularPoojas' => $scheduleModel->getPopularPoojas(),
                    'busyTimeSlots' => $scheduleModel->getBusyTimeSlots(),
                    'monthlyTrends' => $scheduleModel->getMonthlyTrends()
                ];
                
                if ($data && $data->num_rows > 0) {
                    $tempData = $data;
                    $tempData->data_seek(0);
                    while ($row = $tempData->fetch_assoc()) {
                        $stats['total']++;
                        if (isset($stats[$row['status']])) {
                            $stats[$row['status']]++;
                        }
                    }
                }
                break;

            case 'export-poojas':
                $this->checkRole('management');
                session_write_close();
                
                // Export to CSV
                $data = $scheduleModel->getAllWithBookings();
                
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="pooja_history_' . date('Y-m-d') . '.csv"');
                
                $output = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($output, ['ID', 'Pooja Name', 'Date', 'Time Slot', 'Status', 'Booked By', 'Description', 'Created At']);
                
                // CSV Data
                while ($row = $data->fetch_assoc()) {
                    fputcsv($output, [
                        $row['id'],
                        $row['pooja_name'],
                        date('Y-m-d', strtotime($row['pooja_date'])),
                        date('g:i A', strtotime($row['time_slot'])),
                        ucfirst($row['status']),
                        $row['booked_by_name'] ?? 'N/A',
                        $row['description'] ?? '',
                        date('Y-m-d H:i:s', strtotime($row['created_at']))
                    ]);
                }
                
                fclose($output);
                exit;

            case 'export-donations-pdf':
                $this->checkRole('management');
                session_write_close();
                require_once __DIR__ . '/../../config/pdf_helper.php';

                $startDate = $_GET['start_date'] ?? null;
                $endDate = $_GET['end_date'] ?? null;
                $donationRows = $donationModel->getCompletedDonations($startDate, $endDate);
                $filename = 'donations_done_' . date('Y-m-d') . '.pdf';

                outputSimpleDonationPdf($donationRows, $filename, [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);
                exit;

            case 'schedule':
                // Check if action parameter exists (for add new pooja)
                $action = $_GET['action'] ?? '';
                if ($action === 'add') {
                    $this->checkRole('management');
                    // Show add pooja form - render with full layout
                    $view = "schedule/add";
                    require __DIR__ . '/../views/layouts/header.php';
                    include __DIR__ . '/../views/' . $view . '.php';
                    include __DIR__ . '/../views/layouts/footer.php';
                    return;
                } elseif ($action === 'store') {
                    // Store new pooja schedule (POST request)
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'management') {
                        $pooja_name = trim($_POST['pooja_name'] ?? '');
                        $pooja_date = $_POST['pooja_date'] ?? '';
                        $time_slot = $_POST['time_slot'] ?? '';
                        $description = trim($_POST['description'] ?? '');

                        if (empty($pooja_name) || empty($pooja_date) || empty($time_slot)) {
                            $_SESSION['error'] = "Please fill in all required fields";
                        } else {
                            $result = $scheduleModel->create($pooja_name, $pooja_date, $time_slot, $description);
                            
                            if ($result) {
                                $_SESSION['success'] = "Pooja scheduled successfully!";
                            } else {
                                $_SESSION['error'] = "Failed to schedule pooja";
                            }
                        }
                        header("Location: ?url=schedule&action=add");
                        exit;
                    }
                } elseif ($action === 'edit') {
                    // Show edit pooja form - render with full layout
                    $this->checkRole('management');
                    $scheduleId = intval($_GET['id'] ?? 0);
                    if ($scheduleId > 0) {
                        $scheduleData = $scheduleModel->getByIdWithBooking($scheduleId);
                        $approvedDevotees = $userModel->getApprovedDevotees();
                        if (!$scheduleData) {
                            $_SESSION['error'] = "Schedule not found";
                            header("Location: ?url=schedule");
                            exit;
                        }
                        $data = [
                            'schedule' => $scheduleData,
                            'approvedDevotees' => $approvedDevotees
                        ];
                    } else {
                        header("Location: ?url=schedule");
                        exit;
                    }
                    $view = __DIR__ . '/../views/schedule/edit.php';
                } elseif ($action === 'update') {
                    // Update existing pooja schedule (POST request)
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'management') {
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
                            $result = $scheduleModel->update($scheduleId, $pooja_name, $pooja_date, $time_slot, $description);
                            if ($result && in_array($bookingStatus, ['booked', 'available'], true)) {
                                $bookingUpdate = $bookingModel->adminManageScheduleBooking($scheduleId, $bookingStatus, $assignedDevoteeId > 0 ? $assignedDevoteeId : null);
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
                        header("Location: ?url=schedule&action=edit&id=" . $scheduleId);
                        exit;
                    }
                } elseif ($action === 'delete') {
                    // Delete a pooja schedule (POST request)
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'management') {
                        $scheduleId = intval($_POST['id'] ?? 0);
                        if ($scheduleId > 0) {
                            $result = $scheduleModel->delete($scheduleId);
                            if ($result) {
                                $_SESSION['success'] = "Pooja deleted successfully!";
                            } else {
                                $_SESSION['error'] = "Failed to delete pooja";
                            }
                        } else {
                            $_SESSION['error'] = "Invalid schedule ID";
                        }
                        header("Location: ?url=schedule");
                        exit;
                    }
                }
                
                // Only load all schedules if not editing (edit sets its own view)
                if (!isset($view) || $view !== __DIR__ . '/../views/schedule/edit.php') {
                    $filterDate = trim($_GET['filter_date'] ?? '');
                    $isValidFilterDate = $filterDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate);
                    if ($isValidFilterDate) {
                        $data = $scheduleModel->getAllWithBookingsByDate($filterDate);
                    } else {
                        $data = $scheduleModel->getAllWithBookings();
                    }
                }
                break;

            case 'book':
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

                        $errors = $bookingModel->validateBooking($bookingData);
                        
                        if (empty($errors)) {
                            $result = $bookingModel->create($scheduleId, $_SESSION['user']['id'], $devoteePhone, $specialRequests, $notificationPreference);
                            
                            if ($result['success']) {
                                error_log("Booking Success - Booking ID: " . $result['booking_id']);
                                header("Location: ?url=confirmation&booking_id=" . $result['booking_id']);
                                exit;
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
                    if ($scheduleModel->isAvailable($scheduleId)) {
                        $data = $scheduleModel->getById($scheduleId);
                    } else {
                        $error = "This pooja is no longer available for booking";
                    }
                } elseif (empty($error)) {
                    $error = "Invalid schedule selected";
                }
                
                if (!empty($error) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
                    header("Location: ?url=schedule&error=" . urlencode($error));
                    exit;
                }
                break;

            case 'confirmation':
                $action = $_GET['action'] ?? '';

                if ($action === 'download-pdf') {
                    $bookingId = intval($_GET['booking_id'] ?? 0);
                    if ($bookingId <= 0) {
                        header("Location: ?url=schedule");
                        exit;
                    }

                    $bookingData = $bookingModel->getById($bookingId);
                    if (!$bookingData) {
                        $_SESSION['error'] = "Booking not found";
                        header("Location: ?url=schedule");
                        exit;
                    }

                    $currentUser = $_SESSION['user'] ?? null;
                    $canDownload = $currentUser && (
                        ($currentUser['role'] ?? '') === 'management' ||
                        (int) ($bookingData['user_id'] ?? 0) === (int) ($currentUser['id'] ?? 0)
                    );
                    if (!$canDownload) {
                        $_SESSION['error'] = "You are not authorized to download this booking receipt.";
                        header("Location: ?url=my-bookings");
                        exit;
                    }

                    require_once __DIR__ . '/../../config/pdf_helper.php';
                    $bringItems = $bookingModel->getBringItemsForBooking($bookingData);
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
                        $bookingData = $bookingModel->getById($bookingId);
                        
                        if (!$bookingData) {
                            echo json_encode(['success' => false, 'message' => 'Booking not found']);
                            exit;
                        }
                        
                        $result = $bookingModel->sendBookingNotification($bookingId, $channel);
                        
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
                    $data = $bookingModel->getById($bookingId);
                    if (!$data) {
                        $_SESSION['error'] = "Booking not found";
                        header("Location: ?url=schedule");
                        exit;
                    }
                } else {
                    header("Location: ?url=schedule");
                    exit;
                }
                break;

            case 'my-bookings':
                $data = $bookingModel->getByUser($_SESSION['user']['id']);
                break;

            case 'priest':
                $this->checkRole('priest');
                $data = $dutyModel->getByPriest($_SESSION['user']['id']);
                break;

            case 'priest-schedules':
                // Priest can view all schedules and festivals
                if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['priest', 'management'])) {
                    header("Location: ?url=login");
                    exit;
                }
                $schedules = $dutyModel->getAllSchedulesWithAssignments();
                $festivals = $festivalModel->getAll();
                break;

            case 'assign':
                $this->checkRole('management');

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $priestId = intval($_POST['priest'] ?? 0);
                    $scheduleId = intval($_POST['schedule'] ?? 0);

                    // Validate both values are selected
                    if ($priestId <= 0 || $scheduleId <= 0) {
                        $message = "Please select both priest and pooja schedule";
                        $messageType = "error";
                    } else {
                        if ($dutyModel->isAlreadyAssigned($priestId, $scheduleId)) {
                            $message = "This priest is already assigned to this pooja";
                            $messageType = "error";
                        } else {
                            // Check priest availability
                            $schedule = $scheduleModel->getById($scheduleId);
                            if ($schedule && $dutyModel->isPriestAvailable($priestId, $schedule['pooja_date'])) {
                                $result = $dutyModel->assign($priestId, $scheduleId);
                                
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
                $priests = $dutyModel->getPriestAvailability($selectedDate);
                
                // Get all pooja schedules for priest assignment (including past dates for reference)
                // Order by date descending to show most recent first
                $schedules = $scheduleModel->getAllChronological();
                $duties = $dutyModel->getAllDetailed();
                break;

            case 'donation':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $donorName = trim($_POST['name'] ?? '');
                    $amount = floatval($_POST['amount'] ?? 0);
                    $purpose = trim($_POST['purpose'] ?? '');
                    $paymentMethod = trim($_POST['payment_method'] ?? 'card');

                    $donationErrors = $donationModel->validateDonation([
                        'donor_name' => $donorName,
                        'amount' => $amount,
                        'purpose' => $purpose,
                        'payment_method' => $paymentMethod,
                    ]);

                    if (!empty($donationErrors)) {
                        $message = $donationErrors[0];
                        $messageType = "error";
                    } else {
                        $result = $donationModel->create($donorName, $amount, $purpose, $paymentMethod);
                        
                        if ($result['success']) {
                            $message = $result['message'];
                            $messageType = "success";
                            $data = [
                                'receipt' => $donationModel->buildReceiptData(
                                    $result['donation_reference'] ?? ('DON-' . ($result['id'] ?? '')),
                                    $donationModel->normalizeDonorName($donorName),
                                    round($amount, 2),
                                    $donationModel->normalizePurpose($purpose),
                                    $paymentMethod
                                )
                            ];
                        } else {
                            $message = $result['message'];
                            $messageType = "error";
                        }
                    }
                }
                
                $donations = $donationModel->getTotalAmount();
                $role = $_SESSION['user']['role'] ?? '';
                $summaryStats = $donationModel->getSummaryStats();
                $donationList = $role === 'management' ? $donationModel->getAll() : $donationModel->getAllMasked();
                if (!is_array($data)) {
                    $data = [];
                }
                $data['summary'] = $summaryStats;
                $data['donations'] = $donationList;
                break;

            case 'announcement':
                // Check if action parameter exists (for create new announcement)
                $action = $_GET['action'] ?? '';
                if ($action === 'create') {
                    // Show create announcement form - render with full layout
                    $view = "announcement/create";
                    require __DIR__ . '/../views/layouts/header.php';
                    include __DIR__ . '/../views/' . $view . '.php';
                    include __DIR__ . '/../views/layouts/footer.php';
                    return;
                } elseif ($action === 'store') {
                    // Store new announcement (POST request)
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'management') {
                        $title = trim($_POST['title'] ?? '');
                        $message = trim($_POST['message'] ?? '');
                        $date = $_POST['date'] ?? '';
                        $created_by = $_SESSION['user']['id'];

                        if (empty($title) || empty($message) || empty($date)) {
                            $_SESSION['error'] = "Please fill in all required fields";
                        } else {
                            $result = $announcementModel->create($title, $message, $date, $created_by);
                            
                            if ($result['success']) {
                                $_SESSION['success'] = "Announcement created successfully!";
                            } else {
                                $_SESSION['error'] = $result['message'];
                            }
                        }
                        header("Location: ?url=announcement&action=create");
                        exit;
                    }
                }
                $data = $announcementModel->getAll();
                break;

            case 'festival':
                $action = $_GET['action'] ?? '';
                if ($action === 'details') {
                    $festivalId = intval($_GET['id'] ?? 0);
                    if ($festivalId > 0) {
                        $festivalRecord = $festivalModel->getById($festivalId);
                        if (!$festivalRecord) {
                            $_SESSION['error'] = "Festival not found";
                            header("Location: ?url=festival");
                            exit;
                        }
                        $data = [
                            'name' => $festivalRecord['name'],
                            'date' => $festivalRecord['date'],
                            'description' => $festivalRecord['description'] ?? '',
                            'category' => 'Festival',
                            'editable' => true,
                            'id' => $festivalRecord['id']
                        ];
                    } else {
                        $name = trim($_GET['name'] ?? '');
                        $date = trim($_GET['date'] ?? '');
                        $description = trim($_GET['description'] ?? '');
                        $category = trim($_GET['category'] ?? 'Event');
                        if ($name === '' || $date === '') {
                            $_SESSION['error'] = "Festival details not available";
                            header("Location: ?url=festival");
                            exit;
                        }
                        $data = [
                            'name' => $name,
                            'date' => $date,
                            'description' => $description,
                            'category' => $category,
                            'editable' => false,
                            'id' => null
                        ];
                    }
                    $view = "festival/details";
                    require __DIR__ . '/../views/layouts/header.php';
                    include __DIR__ . '/../views/' . $view . '.php';
                    include __DIR__ . '/../views/layouts/footer.php';
                    return;
                } elseif ($action === 'add') {
                    $this->checkRole('management');
                    $view = "festival/add";
                    require __DIR__ . '/../views/layouts/header.php';
                    include __DIR__ . '/../views/' . $view . '.php';
                    include __DIR__ . '/../views/layouts/footer.php';
                    return;
                } elseif ($action === 'store') {
                    $this->checkRole('management');
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $name = trim($_POST['name'] ?? '');
                        $date = $_POST['date'] ?? '';
                        $description = trim($_POST['description'] ?? '');
                        if ($name === '' || $date === '') {
                            $_SESSION['error'] = "Festival name and date are required";
                        } else {
                            $created = $festivalModel->create($name, $date, $description);
                            $_SESSION[$created['success'] ? 'success' : 'error'] = $created['message'];
                        }
                    }
                    header("Location: ?url=festival&action=add");
                    exit;
                } elseif ($action === 'edit') {
                    $this->checkRole('management');
                    $festivalId = intval($_GET['id'] ?? 0);
                    $festivalRecord = $festivalModel->getById($festivalId);
                    if (!$festivalRecord) {
                        $_SESSION['error'] = "Festival not found";
                        header("Location: ?url=festival");
                        exit;
                    }
                    $selectedYear = date('Y', strtotime($festivalRecord['date']));
                    $yearFestivals = $festivalModel->getByYear($selectedYear);
                    $yearSpecialDays = $specialDayModel->getByYear($selectedYear);
                    $yearPoojas = $scheduleModel->getByYear($selectedYear);
                    $data = [
                        'festival' => $festivalRecord,
                        'yearFestivals' => $yearFestivals,
                        'yearSpecialDays' => $yearSpecialDays,
                        'yearPoojas' => $yearPoojas
                    ];
                    $view = __DIR__ . '/../views/festival/edit.php';
                } elseif ($action === 'update') {
                    $this->checkRole('management');
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $festivalId = intval($_POST['id'] ?? 0);
                        $name = trim($_POST['name'] ?? '');
                        $date = $_POST['date'] ?? '';
                        $description = trim($_POST['description'] ?? '');
                        if ($festivalId <= 0 || $name === '' || $date === '') {
                            $_SESSION['error'] = "Festival name and date are required";
                        } else {
                            $ok = $festivalModel->update($festivalId, $name, $date, $description);
                            $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Festival updated successfully!" : "Failed to update festival";
                        }
                        header("Location: ?url=festival&action=edit&id=" . $festivalId);
                        exit;
                    }
                } elseif ($action === 'delete') {
                    $this->checkRole('management');
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $festivalId = intval($_POST['id'] ?? 0);
                        $ok = $festivalId > 0 ? $festivalModel->delete($festivalId) : false;
                        $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Festival deleted successfully!" : "Failed to delete festival";
                    }
                    header("Location: ?url=festival");
                    exit;
                }

                if (!isset($view) || $view !== __DIR__ . '/../views/festival/edit.php') {
                    $filterDate = trim($_GET['filter_date'] ?? '');
                    $isValidFilterDate = $filterDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate);
                    $data = $festivalModel->getFestivalPageItems($isValidFilterDate ? $filterDate : null);
                }
                break;

            case 'report':
                $this->checkRole('management');
                $poojas = $scheduleModel->getTotalCount();
                $donations = $donationModel->getTotalAmount();
                $bookings = $bookingModel->getTotalBookings();
                
                // Calculate average donation per pooja
                $avgDonation = $poojas > 0 ? $donations / $poojas : 0;
                break;

            case 'logout':
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
                header("Location: ?url=login");
                exit;

            case 'language':
                // Handle language switching
                $lang = $_GET['lang'] ?? 'en';
                if (function_exists('setLanguage')) {
                    setLanguage($lang);
                } else {
                    // Fallback if helpers not loaded
                    $supportedLanguages = ['en', 'ta'];
                    if (in_array($lang, $supportedLanguages)) {
                        $_SESSION['language'] = $lang;
                    }
                }

                // Redirect back to the referring page or dashboard
                $referer = $_SERVER['HTTP_REFERER'] ?? '?url=dashboard';
                header("Location: " . $referer);
                exit;

            case 'pooja-request':
                $action = $_GET['action'] ?? '';
                if ($action === 'store') {
                    // Handle pooja request submission
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user'])) {
                        $userId = $_SESSION['user']['id'];
                        $poojaName = trim($_POST['pooja_name'] ?? '');
                        $preferredDate = $_POST['preferred_date'] ?? '';
                        $preferredTimeSlot = $_POST['preferred_time_slot'] ?? '';
                        $specialRequests = trim($_POST['special_requests'] ?? '');

                        if (empty($poojaName) || empty($preferredDate)) {
                            $_SESSION['error'] = "Pooja name and preferred date are required";
                        } else {
                            $result = $poojaRequestModel->create($userId, $poojaName, $preferredDate, $preferredTimeSlot, $specialRequests);
                            if ($result['success']) {
                                $_SESSION['success'] = "Pooja request submitted successfully! We will review and get back to you.";
                            } else {
                                $_SESSION['error'] = $result['message'];
                            }
                        }
                    }
                    header("Location: ?url=schedule");
                    exit;
                } elseif ($action === 'my-requests') {
                    // Show user's pooja requests
                    if (isset($_SESSION['user'])) {
                        $data = $poojaRequestModel->getByUserId($_SESSION['user']['id']);
                    }
                } elseif ($action === 'manage') {
                    // Admin view to manage all pooja requests
                    $this->checkRole('management');
                    $data = $poojaRequestModel->getAll();
                    $view = "pooja-requests/manage";
                    require __DIR__ . '/../views/layouts/header.php';
                    include __DIR__ . '/../views/' . $view . '.php';
                    include __DIR__ . '/../views/layouts/footer.php';
                    return;
                } elseif ($action === 'update-status') {
                    // Update pooja request status (admin only)
                    $this->checkRole('management');
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $requestId = intval($_POST['request_id'] ?? 0);
                        $status = $_POST['status'] ?? '';
                        $adminRemarks = trim($_POST['admin_remarks'] ?? '');

                        if ($requestId > 0 && in_array($status, ['pending', 'approved', 'rejected', 'scheduled'])) {
                            $ok = $poojaRequestModel->updateStatus($requestId, $status, $adminRemarks);
                            $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Request status updated successfully!" : "Failed to update request status";
                        } else {
                            $_SESSION['error'] = "Invalid request";
                        }
                    }
                    header("Location: ?url=pooja-request&action=manage");
                    exit;
                } elseif ($action === 'delete') {
                    // Delete pooja request (admin only)
                    $this->checkRole('management');
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $requestId = intval($_POST['request_id'] ?? 0);
                        $ok = $requestId > 0 ? $poojaRequestModel->delete($requestId) : false;
                        $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Request deleted successfully!" : "Failed to delete request";
                    }
                    header("Location: ?url=pooja-request&action=manage");
                    exit;
                }
                break;

            default:
                // Page not found
                header("Location: ?url=login");
                exit;
        }

        // Load view
        require __DIR__ . "/../views/layouts/header.php";

        // Handle role-specific dashboard views
        // Respect action-specific view selected in switch-case (e.g., schedule edit, festival edit).
        if ($page === 'dashboard') {
            $userRole = $_SESSION['user']['role'] ?? '';
            if ($userRole === 'management') {
                $view = __DIR__ . "/../views/dashboard/admin.php";
            } elseif ($userRole === 'priest') {
                $view = __DIR__ . "/../views/dashboard/priest.php";
            } else {
                $view = __DIR__ . "/../views/dashboard/user.php";
            }
        } elseif (!isset($view)) {
            $view = __DIR__ . "/../views/$page/index.php";
        }
        
        if (file_exists($view)) {
            require $view;
        } else {
            echo "<div class='p-10 text-center text-white'>Page not found: $page</div>";
        }

        require __DIR__ . "/../views/layouts/footer.php";
    }

    /**
     * Check if user has the required role
     */
    private function checkRole($requiredRole) {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $requiredRole) {
            header("Location: ?url=login");
            exit;
        }
    }
}

