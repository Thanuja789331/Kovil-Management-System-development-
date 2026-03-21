<?php
require_once "config/database.php";

class Duty {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function assign($priest, $schedule) {

        $stmt = $this->conn->prepare("
            INSERT INTO priest_duties(priest_id,schedule_id,assigned_date)
            VALUES(?,?,CURDATE())
        ");

        $stmt->bind_param("ii", $priest, $schedule);

        return $stmt->execute();
    }

    public function getByPriest($id) {

        $stmt = $this->conn->prepare("
            SELECT ps.pooja_name, ps.pooja_date, ps.time_slot
            FROM priest_duties pd
            JOIN pooja_schedule ps ON pd.schedule_id = ps.id
            WHERE pd.priest_id=?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result();
    }
}