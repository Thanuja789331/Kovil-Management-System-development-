<?php
session_start();

require_once "config/database.php";
require_once "app/controllers/MainController.php";

$page = $_GET['url'] ?? 'login';

$controller = new MainController();
$controller->load($page);