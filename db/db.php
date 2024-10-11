<?php
$host = 'localhost';
$db = 'avito';
$user = 'uploader';
$pass = 'uploader';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    // Дополнительные настройки, например, чтобы PDO выбрасывало исключение при ошибке
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    die('Подключение не удалось: ' . $e->getMessage());
}
