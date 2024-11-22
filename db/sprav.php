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
    http_response_code(500);
    exit;
}

if ($_POST['sprav']) {
    $sp = $_POST['sprav'];
} else {
    echo json_encode('Пустой справочник!');
    http_response_code(500);
    exit;
}
$arr = [];
$pdo->beginTransaction();
try {
    $sql = "SELECT * FROM brand_sprav WHERE brand = :brand";
    $stmt1 = $pdo->prepare($sql);
    $stmt1->bindParam(':brand', $brand, PDO::PARAM_STR);
    $stmt1->execute();
    $sprav = $stmt1->fetch(PDO::FETCH_COLUMN);

    if ($sprav) {
        $sql1 = 'UPDATE brand_sprav SET sprav = :sprav WHERE brand = :brand';
        $stmt = $pdo->prepare($sql1);

        // Привязка параметров и выполнение запроса
        $stmt->bindParam(':brand', $brand, PDO::PARAM_STR);
        $stmt->bindParam(':sprav', $sp, PDO::PARAM_STR);

        // Выполнение запроса
        if ($stmt->execute()) {
            http_response_code(200);
        } else {
            array_push($arr, ['error' => "Error updating data in the table: $brand/$sp"]);
            $pdo->commit();
            $pdo->rollBack();
            http_response_code(500);
            exit;
        }
    } else {
        $sql1 = 'INSERT INTO brand_sprav (brand, sprav) VALUES (:brand, :sprav)';
        $stmt = $pdo->prepare($sql1);

        // Привязка параметров и выполнение запроса
        $stmt->bindParam(':brand', $brand, PDO::PARAM_STR);
        $stmt->bindParam(':sprav', $sp, PDO::PARAM_STR);

        // Выполнение запроса
        if ($stmt->execute()) {
            http_response_code(200);
        } else {
            array_push($arr, ['error' => "Error adding data to the table: $brand/$sp"]);
            $pdo->commit();
            $pdo->rollBack();
            http_response_code(500);
            exit;
        }
    }

    $pdo->commit();
} catch (Exception $e) {
    // Откат изменений в случае ошибки
    $pdo->rollBack();
    array_push($arr, ['error' => "Error processing files: " . $e->getMessage()]);
}

echo json_encode($arr);