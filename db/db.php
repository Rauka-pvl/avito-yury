<?php
$host = 'localhost';
$db = 'avita_yura';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    // Дополнительные настройки, например, чтобы PDO выбрасывало исключение при ошибке
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die('Подключение не удалось: ' . $e->getMessage());
}
