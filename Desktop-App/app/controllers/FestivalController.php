<?php

require_once __DIR__ . "/../models/Festival.php";
require_once __DIR__ . "/../models/SpecialDay.php";
require_once __DIR__ . "/../models/Schedule.php";
require_once __DIR__ . "/Controller.php";

class FestivalController extends Controller {
    private $festivalModel;
    private $specialDayModel;
    private $scheduleModel;

    public function __construct() {
        parent::__construct();
        $this->festivalModel = new Festival();
        $this->specialDayModel = new SpecialDay();
        $this->scheduleModel = new Schedule();
    }

    /**
     * Display all festivals
     */
    public function index() {
        $filterDate = trim($_GET['filter_date'] ?? '');
        $isValidFilterDate = $filterDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate);
        $data = $this->festivalModel->getFestivalPageItems($isValidFilterDate ? $filterDate : null);
        return ['data' => $data];
    }

    /**
     * Show festival details
     */
    public function details() {
        $festivalId = intval($_GET['id'] ?? 0);
        if ($festivalId > 0) {
            $festivalRecord = $this->festivalModel->getById($festivalId);
            if (!$festivalRecord) {
                $_SESSION['error'] = "Festival not found";
                $this->redirect("?url=festival");
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
                $this->redirect("?url=festival");
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

        $view = __DIR__ . "/../views/festival/details.php";
        $this->loadView($view, $data);
    }

    /**
     * Show add festival form
     */
    public function add() {
        $this->checkRole('management');
        $view = __DIR__ . "/../views/festival/add.php";
        $this->loadView($view);
    }

    /**
     * Store new festival
     */
    public function store() {
        $this->checkRole('management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $date = $_POST['date'] ?? '';
            $description = trim($_POST['description'] ?? '');
            if ($name === '' || $date === '') {
                $_SESSION['error'] = "Festival name and date are required";
            } else {
                $created = $this->festivalModel->create($name, $date, $description);
                $_SESSION[$created['success'] ? 'success' : 'error'] = $created['message'];
            }
        }
        
        $this->redirect("?url=festival&action=add");
    }

    /**
     * Show edit festival form
     */
    public function edit() {
        $this->checkRole('management');
        
        $festivalId = intval($_GET['id'] ?? 0);
        $festivalRecord = $this->festivalModel->getById($festivalId);
        if (!$festivalRecord) {
            $_SESSION['error'] = "Festival not found";
            $this->redirect("?url=festival");
        }
        $selectedYear = date('Y', strtotime($festivalRecord['date']));
        $yearFestivals = $this->festivalModel->getByYear($selectedYear);
        $yearSpecialDays = $this->specialDayModel->getByYear($selectedYear);
        $yearPoojas = $this->scheduleModel->getByYear($selectedYear);
        $data = [
            'festival' => $festivalRecord,
            'yearFestivals' => $yearFestivals,
            'yearSpecialDays' => $yearSpecialDays,
            'yearPoojas' => $yearPoojas
        ];

        $view = __DIR__ . "/../views/festival/edit.php";
        $this->loadView($view, $data);
    }

    /**
     * Update festival
     */
    public function update() {
        $this->checkRole('management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $festivalId = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $date = $_POST['date'] ?? '';
            $description = trim($_POST['description'] ?? '');
            if ($festivalId <= 0 || $name === '' || $date === '') {
                $_SESSION['error'] = "Festival name and date are required";
            } else {
                $ok = $this->festivalModel->update($festivalId, $name, $date, $description);
                $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Festival updated successfully!" : "Failed to update festival";
            }
            $this->redirect("?url=festival&action=edit&id=" . $festivalId);
        }
    }

    /**
     * Delete festival
     */
    public function delete() {
        $this->checkRole('management');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $festivalId = intval($_POST['id'] ?? 0);
            $ok = $festivalId > 0 ? $this->festivalModel->delete($festivalId) : false;
            $_SESSION[$ok ? 'success' : 'error'] = $ok ? "Festival deleted successfully!" : "Failed to delete festival";
        }
        
        $this->redirect("?url=festival");
    }
}
