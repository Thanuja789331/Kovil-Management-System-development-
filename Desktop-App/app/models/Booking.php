<?php
require_once "config/database.php";

class Booking {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function create($schedule_id, $user_id) {

        $stmt = $this->conn->prepare("
            INSERT INTO bookings(schedule_id,user_id,created_at)
            VALUES(?,?,NOW())
        ");

        $stmt->bind_param("ii", $schedule_id, $user_id);

        return $stmt->execute();
    }
}