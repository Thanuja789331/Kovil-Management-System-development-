<?php

require_once __DIR__ . "/../models/Donation.php";
require_once __DIR__ . "/Controller.php";

class DonationController extends Controller {
    private $donationModel;

    public function __construct() {
        parent::__construct();
        $this->donationModel = new Donation();
    }

    /**
     * Handle donation page
     */
    public function index() {
        $message = "";
        $messageType = "";
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donorName = trim($_POST['name'] ?? '');
            $amount = floatval($_POST['amount'] ?? 0);
            $purpose = trim($_POST['purpose'] ?? '');
            $paymentMethod = trim($_POST['payment_method'] ?? 'card');

            $donationErrors = $this->donationModel->validateDonation([
                'donor_name' => $donorName,
                'amount' => $amount,
                'purpose' => $purpose,
                'payment_method' => $paymentMethod,
            ]);

            if (!empty($donationErrors)) {
                $message = $donationErrors[0];
                $messageType = "error";
            } else {
                $result = $this->donationModel->create($donorName, $amount, $purpose, $paymentMethod);
                
                if ($result['success']) {
                    $message = $result['message'];
                    $messageType = "success";
                    $data = [
                        'receipt' => $this->donationModel->buildReceiptData(
                            $result['donation_reference'] ?? ('DON-' . ($result['id'] ?? '')),
                            $this->donationModel->normalizeDonorName($donorName),
                            round($amount, 2),
                            $this->donationModel->normalizePurpose($purpose),
                            $paymentMethod
                        )
                    ];
                } else {
                    $message = $result['message'];
                    $messageType = "error";
                }
            }
        }
        
        $role = $_SESSION['user']['role'] ?? '';
        $startDate = trim($_GET['start_date'] ?? '');
        $endDate = trim($_GET['end_date'] ?? '');
        $isFiltered = ($startDate !== '' && $endDate !== '');

        if ($isFiltered) {
            $donations = $this->donationModel->getTotalAmountInRange($startDate, $endDate);
            $donationList = $this->donationModel->getByDateRange($startDate, $endDate);
        } else {
            $donations = $this->donationModel->getTotalAmount();
            $donationList = $role === 'management' ? $this->donationModel->getAll() : $this->donationModel->getAllMasked();
        }

        $summaryStats = $this->donationModel->getSummaryStats();
        if (!is_array($data)) {
            $data = [];
        }
        $data['summary'] = $summaryStats;
        $data['donations'] = $donationList;

        return [
            'message' => $message,
            'messageType' => $messageType,
            'data' => $data,
            'donations' => $donations
        ];
    }
}
