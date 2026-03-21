<?php
require_once "config/database.php";

class User {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function login($email, $password) {

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function register($name, $email, $password, $role) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("
            INSERT INTO users(name,email,password,role)
            VALUES(?,?,?,?)
        ");

        $stmt->bind_param("ssss", $name, $email, $hash, $role);

        return $stmt->execute();
    }
}