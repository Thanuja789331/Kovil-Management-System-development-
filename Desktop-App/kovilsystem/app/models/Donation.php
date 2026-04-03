<?php
require_once __DIR__ . "/../../config/database.php";

class Donation {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Create a new donation
     */
    public function create($donor_name, $amount, $purpose) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO donations (donor_name, amount, purpose, payment_status) VALUES (?, ?, ?, 'completed')");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("sds", $donor_name, $amount, $purpose);
            $success = $stmt->execute();
            $insertId = $stmt->insert_id;
            $stmt->close();
            
            return $success ? 
                ["success" => true, "message" => "Donation recorded successfully", "id" => $insertId] :
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
}
