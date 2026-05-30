<?php

require_once __DIR__ . "/../models/Schedule.php";
require_once __DIR__ . "/../models/Booking.php";
require_once __DIR__ . "/../models/Donation.php";
require_once __DIR__ . "/Controller.php";

class ReportController extends Controller {
    private $scheduleModel;
    private $bookingModel;
    private $donationModel;

    public function __construct() {
        parent::__construct();
        $this->scheduleModel = new Schedule();
        $this->bookingModel = new Booking();
        $this->donationModel = new Donation();
    }

    /**
     * Handle report page
     */
    public function index() {
        $this->checkRole('management');
        
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate = trim($_GET['end_date'] ?? '');
        $isFiltered = ($startDate !== '' && $endDate !== '');

        if ($isFiltered) {
            $poojas = $this->scheduleModel->getTotalCountFilteredByRange($startDate, $endDate);
            $donations = $this->donationModel->getTotalAmountInRange($startDate, $endDate);
            $bookings = $this->bookingModel->getTotalBookingsInRange($startDate, $endDate);
        } else {
            $poojas = $this->scheduleModel->getTotalCount();
            $donations = $this->donationModel->getTotalAmount();
            $bookings = $this->bookingModel->getTotalBookings();
        }
        
        // Calculate average donation per pooja
        $avgDonation = $poojas > 0 ? $donations / $poojas : 0;

        return [
            'poojas' => $poojas,
            'donations' => $donations,
            'bookings' => $bookings,
            'avgDonation' => $avgDonation
        ];
    }

    /**
     * Handle pooja history
     */
    public function poojaHistory() {
        // Pagination
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $dateFilter = trim($_GET['date_filter'] ?? '');

        // Get all poojas with booking information and pagination
        $data = $this->scheduleModel->getAllWithBookingsPaginated($perPage, $offset, $search, $status, $dateFilter);
        $totalPoojas = $this->scheduleModel->getTotalCount($search, $status, $dateFilter);
        $totalPages = ceil($totalPoojas / $perPage);
        
        // Get analytics data
        $analytics = [
            'popularPoojas' => $this->scheduleModel->getPopularPoojas(),
            'busyTimeSlots' => $this->scheduleModel->getBusyTimeSlots(),
            'monthlyTrends' => $this->scheduleModel->getMonthlyTrends()
        ];
        
        // Get database statistics
        $stats = $this->scheduleModel->getPoojaStats($search, $status, $dateFilter);

        return [
            'data' => $data,
            'stats' => $stats,
            'analytics' => $analytics,
            'page' => $page,
            'totalPages' => $totalPages
        ];
    }

    /**
     * Export poojas to CSV
     */
    public function exportPoojas() {
        $this->checkRole('management');
        session_write_close();
        
        // Export to CSV
        $data = $this->scheduleModel->getAllWithBookings();
        
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
    }

    /**
     * Export donations to PDF
     */
    public function exportDonationsPdf() {
        $this->checkRole('management');
        session_write_close();
        require_once __DIR__ . '/../../config/pdf_helper.php';

        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $donationRows = $this->donationModel->getCompletedDonations($startDate, $endDate);
        $filename = 'donations_done_' . date('Y-m-d') . '.pdf';

        outputSimpleDonationPdf($donationRows, $filename, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        exit;
    }

    /**
     * Export donations to CSV
     */
    public function exportDonationsCsv() {
        $this->checkRole('management');
        session_write_close();

        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $donationRows = $this->donationModel->getCompletedDonations($startDate, $endDate);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="donations_report_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($output, ['Donor Name', 'Amount ($)', 'Purpose', 'Payment Status', 'Donation Date']);
        
        // CSV Data
        foreach ($donationRows as $row) {
            fputcsv($output, [
                $row['donor_name'],
                number_format($row['amount'], 2),
                $row['purpose'],
                ucfirst($row['payment_status']),
                date('Y-m-d H:i:s', strtotime($row['created_at']))
            ]);
        }
        
        fclose($output);
        exit;
    }
}
