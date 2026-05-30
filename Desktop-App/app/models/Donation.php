<?php
require_once __DIR__ . "/../../config/database.php";

class Donation {
    private $conn;
    private static $columnsChecked = false;
    private const MIN_AMOUNT = 1.00;
    private const MAX_AMOUNT = 999999.99;
    private const MAX_PURPOSE_LENGTH = 200;
    private const MAX_DONOR_NAME_LENGTH = 100;

    public function __construct() {
        $this->conn = Database::connect();
        $this->ensureTable();
        $this->ensureColumns();
    }

    /**
     * Validate donation input before persistence.
     */
    public function validateDonation(array $data) {
        $errors = [];
        $donorName = $this->normalizeDonorName($data['donor_name'] ?? $data['name'] ?? '');
        $amount = isset($data['amount']) ? (float) $data['amount'] : 0;
        $purpose = trim((string) ($data['purpose'] ?? ''));
        $paymentMethod = trim((string) ($data['payment_method'] ?? 'card'));

        if (!$this->isValidDonorName($donorName)) {
            $errors[] = "Donor name must contain only letters/spaces and at least 2 letters.";
        }
        if (!$this->isValidAmount($amount)) {
            $errors[] = "Please enter a valid donation amount between $" . number_format(self::MIN_AMOUNT, 2) . " and $" . number_format(self::MAX_AMOUNT, 2) . ".";
        }
        if (!$this->isValidPurpose($purpose)) {
            $errors[] = "Purpose must be 3–" . self::MAX_PURPOSE_LENGTH . " characters using letters, numbers, spaces, or basic punctuation.";
        }
        if (!$this->isValidPaymentMethod($paymentMethod)) {
            $errors[] = "Please select a valid payment method.";
        }

        return $errors;
    }

    /**
     * Create a new donation
     */
    public function create($donor_name, $amount, $purpose, $paymentMethod = 'card') {
        try {
            $donor_name = $this->normalizeDonorName($donor_name);
            $purpose = $this->normalizePurpose($purpose);
            $paymentMethod = trim((string) $paymentMethod);
            $amount = round((float) $amount, 2);

            $validationErrors = $this->validateDonation([
                'donor_name' => $donor_name,
                'amount' => $amount,
                'purpose' => $purpose,
                'payment_method' => $paymentMethod,
            ]);
            if (!empty($validationErrors)) {
                return ["success" => false, "message" => $validationErrors[0]];
            }

            $donationReference = 'DON' . strtoupper(uniqid());
            $stmt = $this->conn->prepare("INSERT INTO donations (donor_name, amount, purpose, payment_method, donation_reference, payment_status) VALUES (?, ?, ?, ?, ?, 'completed')");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("sdsss", $donor_name, $amount, $purpose, $paymentMethod, $donationReference);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $success ? 
                [
                    "success" => true,
                    "message" => "Donation recorded successfully",
                    "id" => $insertId,
                    "donation_reference" => $donationReference,
                    "payment_method" => $paymentMethod
                ] :
                ["success" => false, "message" => "Failed to record donation"];
        } catch (Exception $e) {
            error_log("Donation error: " . $e->getMessage());
            return ["success" => false, "message" => "An error occurred while processing donation"];
        }
    }

    /**
     * Get all donations
     */
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM donations ORDER BY created_at DESC");
        return $result;
    }

    public function getAllMasked() {
        $result = $this->conn->query("
            SELECT amount, purpose, payment_method, donation_reference, created_at
            FROM donations
            ORDER BY created_at DESC
        ");
        return $result;
    }

    /**
     * Get total donation amount
     */
    public function getTotalAmount() {
        $result = $this->conn->query("SELECT SUM(amount) as total FROM donations WHERE payment_status = 'completed'");
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    /**
     * Get total donation amount within a date range
     */
    public function getTotalAmountInRange($startDate, $endDate) {
        $stmt = $this->conn->prepare("SELECT SUM(amount) as total FROM donations WHERE payment_status = 'completed' AND DATE(created_at) BETWEEN ? AND ?");
        $stmt->bind_param("ss", $startDate, $endDate);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row['total'] ?? 0;
    }

    /**
     * Get donations by date range
     */
    public function getByDateRange($start_date, $end_date) {
        $stmt = $this->conn->prepare("SELECT * FROM donations WHERE DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC");
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    /**
     * Get completed donations for report export.
     */
    public function getCompletedDonations($startDate = null, $endDate = null) {
        if (!empty($startDate) && !empty($endDate)) {
            $stmt = $this->conn->prepare("
                SELECT donor_name, amount, purpose, payment_status, created_at
                FROM donations
                WHERE payment_status = 'completed' AND DATE(created_at) BETWEEN ? AND ?
                ORDER BY created_at DESC
            ");
            $stmt->bind_param("ss", $startDate, $endDate);
        } else {
            $stmt = $this->conn->prepare("
                SELECT donor_name, amount, purpose, payment_status, created_at
                FROM donations
                WHERE payment_status = 'completed'
                ORDER BY created_at DESC
            ");
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    public function getSummaryStats() {
        $summary = [
            'weekly_total' => 0,
            'monthly_total' => 0,
            'yearly_total' => 0,
            'all_time_total' => 0
        ];

        $weekly = $this->conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM donations WHERE payment_status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        if ($weekly) {
            $summary['weekly_total'] = (float) ($weekly->fetch_assoc()['total'] ?? 0);
        }

        $monthly = $this->conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM donations WHERE payment_status = 'completed' AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
        if ($monthly) {
            $summary['monthly_total'] = (float) ($monthly->fetch_assoc()['total'] ?? 0);
        }

        $yearly = $this->conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM donations WHERE payment_status = 'completed' AND YEAR(created_at) = YEAR(CURDATE())");
        if ($yearly) {
            $summary['yearly_total'] = (float) ($yearly->fetch_assoc()['total'] ?? 0);
        }

        $allTime = $this->conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM donations WHERE payment_status = 'completed'");
        if ($allTime) {
            $summary['all_time_total'] = (float) ($allTime->fetch_assoc()['total'] ?? 0);
        }

        return $summary;
    }

    /**
     * Validate donor name:
     * - letters and spaces only
     * - at least 2 alphabetic characters
     */
    public function isValidDonorName($donorName) {
        $donorName = $this->normalizeDonorName($donorName);
        if ($donorName === '' || strlen($donorName) > self::MAX_DONOR_NAME_LENGTH) {
            return false;
        }
        if (!preg_match('/^[\p{L} ]+$/u', $donorName)) {
            return false;
        }
        $letterCount = preg_match_all('/\p{L}/u', $donorName);
        return $letterCount >= 2;
    }

    public function isValidAmount($amount) {
        if (!is_numeric($amount)) {
            return false;
        }
        $amount = round((float) $amount, 2);
        if ($amount < self::MIN_AMOUNT || $amount > self::MAX_AMOUNT) {
            return false;
        }
        return abs($amount - round($amount, 2)) < 0.001;
    }

    public function isValidPurpose($purpose) {
        $purpose = trim((string) $purpose);
        if ($purpose === '') {
            return true;
        }
        if (strlen($purpose) < 3 || strlen($purpose) > self::MAX_PURPOSE_LENGTH) {
            return false;
        }
        return (bool) preg_match('/^[\p{L}\p{N}\s.,\-\'&()\/]+$/u', $purpose);
    }

    /**
     * Validate allowed payment method values.
     */
    public function isValidPaymentMethod($paymentMethod) {
        return in_array(trim((string) $paymentMethod), ['card', 'online_transfer'], true);
    }

    public function normalizeDonorName($donorName) {
        $donorName = trim((string) $donorName);
        return preg_replace('/\s+/u', ' ', $donorName);
    }

    public function normalizePurpose($purpose) {
        $purpose = trim((string) $purpose);
        if ($purpose === '') {
            return 'General';
        }
        return preg_replace('/\s+/u', ' ', $purpose);
    }

    /**
     * Build a normalized donation receipt payload for the view layer.
     */
    public function buildReceiptData($reference, $name, $amount, $purpose, $paymentMethod, $createdAt = null) {
        return [
            'reference' => $reference,
            'name' => $name,
            'amount' => (float) $amount,
            'purpose' => $purpose,
            'payment_method' => $paymentMethod,
            'created_at' => $createdAt ?? date('Y-m-d H:i:s')
        ];
    }

    private function ensureTable() {
        $exists = $this->conn->query("SHOW TABLES LIKE 'donations'");
        if ($exists && $exists->num_rows > 0) {
            return;
        }

        $this->conn->query("
            CREATE TABLE IF NOT EXISTS donations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                donor_name VARCHAR(100) NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                purpose VARCHAR(200) NOT NULL DEFAULT 'General',
                payment_method ENUM('card', 'online_transfer') NOT NULL DEFAULT 'card',
                donation_reference VARCHAR(50) NOT NULL,
                payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_donation_reference (donation_reference),
                INDEX idx_created_at (created_at),
                INDEX idx_payment_status (payment_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    private function ensureColumns() {
        if (self::$columnsChecked) {
            return;
        }

        if (!$this->tableExists('donations')) {
            return;
        }

        $columns = [
            'payment_method' => "ALTER TABLE donations ADD COLUMN payment_method ENUM('card','online_transfer') NOT NULL DEFAULT 'card' AFTER purpose",
            'donation_reference' => "ALTER TABLE donations ADD COLUMN donation_reference VARCHAR(50) NULL AFTER payment_method"
        ];
        foreach ($columns as $column => $sql) {
            if (!$this->columnExists('donations', $column)) {
                $this->conn->query($sql);
            }
        }

        $this->conn->query("
            UPDATE donations
            SET donation_reference = CONCAT('DON', UPPER(SUBSTRING(MD5(CONCAT(id, COALESCE(created_at, NOW()))), 1, 12)))
            WHERE donation_reference IS NULL OR donation_reference = ''
        ");

        if (!$this->indexExists('donations', 'uq_donation_reference')) {
            $this->conn->query("ALTER TABLE donations ADD UNIQUE INDEX uq_donation_reference (donation_reference)");
        }

        self::$columnsChecked = true;
    }

    private function indexExists($table, $indexName) {
        $table = $this->conn->real_escape_string($table);
        $indexName = $this->conn->real_escape_string($indexName);
        $result = $this->conn->query("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        return $result && $result->num_rows > 0;
    }

    private function tableExists($table) {
        $table = $this->conn->real_escape_string($table);
        $result = $this->conn->query("SHOW TABLES LIKE '{$table}'");
        return $result && $result->num_rows > 0;
    }

    private function columnExists($table, $column) {
        if (!$this->tableExists($table)) {
            return false;
        }
        $table = $this->conn->real_escape_string($table);
        $column = $this->conn->real_escape_string($column);
        $result = $this->conn->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        return $result && $result->num_rows > 0;
    }
}
