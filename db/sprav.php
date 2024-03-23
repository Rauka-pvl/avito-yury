<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo json_encode('auth:error');
    exit;
}
require_once 'db.php';

var_dump($_POST);