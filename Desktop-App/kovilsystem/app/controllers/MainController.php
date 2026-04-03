<?php

require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Schedule.php";
require_once __DIR__ . "/../models/Booking.php";
require_once __DIR__ . "/../models/Duty.php";
require_once __DIR__ . "/../models/Donation.php";
require_once __DIR__ . "/../models/Announcement.php";
require_once __DIR__ . "/../models/Festival.php";

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

        // ===========================================
        // AUTHENTICATION CHECK
        // Session is already initialized in bootstrap.php
        // ===========================================
        $publicPages = ['login', 'register', 'logout', 'home'];
        
        if (!in_array($page, $publicPages)) {
            // Check if user is logged in
            if (!isset($_SESSION['user']) || empty($_SESSION['user']['id'])) {
                // Debug: Log why auth failed (remove in production)
                error_log("Auth failed for page '$page'. Session user: " . (isset($_SESSION['user']) ? json_encode($_SESSION['user']) : 'NOT SET'));
                
                header("Location: ?url=login");
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
                                $error = "⏳ Your account is pending admin approval. Please wait for approval before logging in.<br><br><small>Admin will review your registration shortly.</small>";
                            } elseif ($checkUser && $checkUser['approval_status'] === 'rejected') {
                                $error = "❌ Your account has been rejected. Please contact admin for more information.";
                            } elseif ($checkUser) {
                                // User exists but wrong password or role mismatch
                                $error = "❌ Invalid password. Please try again.<br><small>Hint: Default password is 'password'</small>";
                            } else {
                                // User doesn't exist at all
                                $error = "❌ No account found with this email address.<br><small>Please register first or check your email.</small>";
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
                    $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM pooja_schedule");
                    $stmt->execute();
                    $poojas = $stmt->get_result()->fetch_assoc()['total'];
                    $stmt->close();
                    
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

            case 'schedule':
                // Check if action parameter exists (for add new pooja)
                $action = $_GET['action'] ?? '';
                if ($action === 'add') {
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
                        $data = $scheduleModel->getById($scheduleId);
                        if (!$data) {
                            $_SESSION['error'] = "Schedule not found";
                            header("Location: ?url=schedule");
                            exit;
                        }
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

                        if (empty($pooja_name) || empty($pooja_date) || empty($time_slot) || $scheduleId <= 0) {
                            $_SESSION['error'] = "Please fill in all required fields";
                        } else {
                            $result = $scheduleModel->update($scheduleId, $pooja_name, $pooja_date, $time_slot, $description);
                            
                            if ($result) {
                                $_SESSION['success'] = "Pooja updated successfully!";
                            } else {
                                $_SESSION['error'] = "Failed to update pooja";
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
                    $data = $scheduleModel->getAllWithBookings();
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
                    
                    // Validate required fields
                    if ($scheduleId <= 0) {
                        $error = "Invalid schedule ID";
                        error_log("Booking Error: Invalid schedule ID");
                    } elseif (empty($_SESSION['user']['id'])) {
                        $error = "User not logged in";
                        error_log("Booking Error: User not logged in");
                    } elseif (empty($devoteePhone)) {
                        $error = "Phone number is required";
                    } elseif (!preg_match('/^[0-9]{10}$/', $devoteePhone)) {
                        $error = "Please enter a valid 10-digit phone number";
                    } else {
                        // Validate booking data
                        $bookingData = [
                            'schedule_id' => $scheduleId,
                            'phone' => $devoteePhone
                        ];
                        
                        $errors = $bookingModel->validateBooking($bookingData);
                        
                        if (empty($errors)) {
                            $result = $bookingModel->create($scheduleId, $_SESSION['user']['id'], $devoteePhone, $specialRequests);
                            
                            if ($result['success']) {
                                // Mark schedule as booked
                                $scheduleModel->markBooked($scheduleId);
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
                // Handle SMS sending request
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['phone'])) {
                    header('Content-Type: application/json');
                    
                    $bookingId = intval($_POST['booking_id']);
                    $phone = $_POST['phone'];
                    
                    try {
                        // Get booking details
                        $bookingData = $bookingModel->getById($bookingId);
                        
                        if (!$bookingData) {
                            echo json_encode(['success' => false, 'message' => 'Booking not found']);
                            exit;
                        }
                        
                        // Prepare confirmation message
                        $message = "🛕 POOJA BOOKING CONFIRMATION 🛕\n\n";
                        $message .= "Booking Reference: {$bookingData['booking_reference']}\n";
                        $message .= "Pooja: {$bookingData['pooja_name']}\n";
                        $message .= "Date: " . date("M j, Y", strtotime($bookingData['pooja_date'])) . "\n";
                        $message .= "Time: {$bookingData['time_slot']}\n";
                        $message .= "Booked By: {$bookingData['user_name']}\n";
                        $message .= "Phone: {$bookingData['phone']}\n\n";
                        
                        if (!empty($bookingData['special_requests'])) {
                            $message .= "Special Requests: {$bookingData['special_requests']}\n\n";
                        }
                        
                        $message .= "Status: Confirmed ✓\n\n";
                        $message .= "Please arrive 15 minutes early.\n";
                        $message .= "Show this message at the temple.\n\n";
                        $message .= "Thank you for your devotion! 🙏";
                        
                        // Send SMS
                        require_once __DIR__ . '/../../config/sms_helper.php';
                        $result = sendSMS($phone, $message, 'booking_confirmation');
                        
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
                        // Check if already assigned
                        $checkStmt = $this->conn->prepare("SELECT id FROM priest_duties WHERE priest_id = ? AND schedule_id = ?");
                        $checkStmt->bind_param("ii", $priestId, $scheduleId);
                        $checkStmt->execute();
                        $result = $checkStmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            $checkStmt->close();
                            $message = "This priest is already assigned to this pooja";
                            $messageType = "error";
                        } else {
                            $checkStmt->close();
                            
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
                $schedules = $this->conn->query("
                    SELECT * FROM pooja_schedule 
                    ORDER BY pooja_date DESC, time_slot ASC
                ");
                break;

            case 'donation':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $donorName = trim($_POST['name'] ?? '');
                    $amount = floatval($_POST['amount'] ?? 0);
                    $purpose = trim($_POST['purpose'] ?? '');

                    if (empty($donorName) || $amount <= 0) {
                        $message = "Please enter valid donor name and amount";
                        $messageType = "error";
                    } else {
                        $result = $donationModel->create($donorName, $amount, $purpose);
                        
                        if ($result['success']) {
                            $message = $result['message'];
                            $messageType = "success";
                        } else {
                            $message = $result['message'];
                            $messageType = "error";
                        }
                    }
                }
                
                $data = $donationModel->getAll();
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
                $data = $festivalModel->getUpcoming();
                break;

            case 'report':
                $this->checkRole('management');
                
                $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM pooja_schedule");
                $stmt->execute();
                $poojas = $stmt->get_result()->fetch_assoc()['total'];
                $stmt->close();
                
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

            default:
                // Page not found
                header("Location: ?url=login");
                exit;
        }

        // Load view
        require __DIR__ . "/../views/layouts/header.php";

        // Handle role-specific dashboard views
        if ($page === 'dashboard') {
            $userRole = $_SESSION['user']['role'] ?? '';
            if ($userRole === 'management') {
                $view = __DIR__ . "/../views/dashboard/admin.php";
            } elseif ($userRole === 'priest') {
                $view = __DIR__ . "/../views/dashboard/priest.php";
            } else {
                $view = __DIR__ . "/../views/dashboard/user.php";
            }
        } else {
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

