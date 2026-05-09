<?php
require_once __DIR__ . "/../../config/database.php";

class SpecialDay {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function getByYear($year) {
        $stmt = $this->conn->prepare("
            SELECT id, title, day_date, description
            FROM special_days
            WHERE YEAR(day_date) = ?
            ORDER BY day_date ASC
        ");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
}
