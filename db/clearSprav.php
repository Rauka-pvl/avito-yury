<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo ('auth:error');
    exit;
}
require_once 'db.php';
if ($_POST['brand']) {
    $brand = $_POST['brand'];
} else {
    echo ('Пустой бранд!');
    http_response_code(500);
    exit;
}

if ($_POST['sprav']) {
    $sp = $_POST['sprav'];
} else {
    echo ('Пустой справочник!');
    http_response_code(500);
    exit;
}
$arr = [];
$pdo->beginTransaction();
try {
    $sql = "DELETE FROM brand_sprav WHERE brand = :brand";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':brand', $brand, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Успешно удалено
        http_response_code(200);
    } else {
        // Произошла ошибка при удалении
        array_push($arr, ['error' => "Error deleting data from the table: $brand"]);
        $pdo->commit();
        $pdo->rollBack();
        http_response_code(500);
    }
    $pdo->commit();
} catch (Exception $e) {
    // Откат изменений в случае ошибки
    $pdo->rollBack();
    array_push($arr, ['error' => "Error processing files: " . $e->getMessage()]);
}

echo $arr;