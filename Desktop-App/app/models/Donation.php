<?php
require_once __DIR__ . "/../../config/database.php";

class Donation {
    private $conn;
    private static $columnsChecked = false;

    public function __construct() {
        $this->conn = Database::connect();
        $this->ensureColumns();
    }

    /**
     * Create a new donation
     */
    public function create($donor_name, $amount, $purpose, $paymentMethod = 'card') {
        try {
            if (!$this->isValidDonorName($donor_name)) {
                return ["success" => false, "message" => "Donor name must contain only letters/spaces and at least 2 letters."];
            }
            if (!$this->isValidPaymentMethod($paymentMethod)) {
                return ["success" => false, "message" => "Invalid payment method selected"];
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
        $donorName = trim((string) $donorName);
        if ($donorName === '') {
            return false;
        }
        if (!preg_match('/^[A-Za-z ]+$/', $donorName)) {
            return false;
        }
        $letterCount = preg_match_all('/[A-Za-z]/', $donorName);
        return $letterCount >= 2;
    }

    /**
     * Validate allowed payment method values.
     */
    public function isValidPaymentMethod($paymentMethod) {
        return in_array(trim((string) $paymentMethod), ['card', 'online_transfer'], true);
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

    private function ensureColumns() {
        if (self::$columnsChecked) {
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
        self::$columnsChecked = true;
    }

    private function columnExists($table, $column) {
        $table = $this->conn->real_escape_string($table);
        $column = $this->conn->real_escape_string($column);
        $result = $this->conn->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        return $result && $result->num_rows > 0;
    }
}
