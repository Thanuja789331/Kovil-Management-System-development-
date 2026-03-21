<?php
require_once "config/database.php";

class Schedule {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function getAll() {
        return $this->conn->query("
            SELECT * FROM pooja_schedule
            ORDER BY pooja_date ASC
        ");
    }

    public function markBooked($id) {
        $id = intval($id);
        return $this->conn->query("
            UPDATE pooja_schedule 
            SET status='booked' 
            WHERE id=$id
        ");
    }
}