<?php

require_once __DIR__ . "/../models/Announcement.php";
require_once __DIR__ . "/Controller.php";

class AnnouncementController extends Controller {
    private $announcementModel;

    public function __construct() {
        parent::__construct();
        $this->announcementModel = new Announcement();
    }

    /**
     * Display all announcements
     */
    public function index() {
        $data = $this->announcementModel->getAll();
        return ['data' => $data];
    }

    /**
     * Show create announcement form
     */
    public function create() {
        $view = __DIR__ . "/../views/announcement/create.php";
        $this->loadView($view);
    }

    /**
     * Store new announcement
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'management') {
            $title = trim($_POST['title'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $date = $_POST['date'] ?? '';
            $created_by = $_SESSION['user']['id'];

            if (empty($title) || empty($message) || empty($date)) {
                $_SESSION['error'] = "Please fill in all required fields";
            } else {
                $result = $this->announcementModel->create($title, $message, $date, $created_by);
                
                if ($result['success']) {
                    $_SESSION['success'] = "Announcement created successfully!";
                } else {
                    $_SESSION['error'] = $result['message'];
                }
            }
        }
        
        $this->redirect("?url=announcement&action=create");
    }
}
