<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo json_encode('auth:error');
    exit;
}
require_once 'db.php';
if ($_POST['brand']) {
    $brand = $_POST['brand'];
} else {
    echo json_encode('Пустой бранд!');
    http_response_code(400);
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

echo json_encode($arr);