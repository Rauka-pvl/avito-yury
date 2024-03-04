<?php
ini_set('max_execution_time', 3600);
session_start();

// Подключение к базе данных (предполагается, что $pdo уже установлено)
require_once 'db.php';

// Получаем JSON-данные из POST-запроса
$jsonData = $_POST['data'];
$data = json_decode($jsonData);

// Дополнительная проверка на корректность декодирования JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Ошибка неверного запроса
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

$arr = [];

// Начало транзакции
$pdo->beginTransaction();

try {
    foreach ($data as $key => $d) {
        $brand = trim(strtolower($d->brand), " ");
        $articul = trim(strtolower($d->fileName));

        $uploadDirectory = "../uploads/" . $brand . "/";
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $uploadPath = $uploadDirectory . $articul;

        // Декодируем данные в двоичный формат
        $binaryData = base64_decode($d->photoSrc);

        // Сохраняем двоичные данные в файл
        if (file_exists($uploadPath)) {
            unlink($uploadPath);
            file_put_contents($uploadPath, $binaryData);
        } else if (file_put_contents($uploadPath, $binaryData) !== false) {
            $sql = 'INSERT INTO images (brand, articul) VALUES (:brand, :articul) ON DUPLICATE KEY UPDATE articul = :articul';
            $stmt = $pdo->prepare($sql);

            // Привязка параметров и выполнение запроса
            $stmt->bindParam(':brand', $brand, PDO::PARAM_STR);
            $stmt->bindParam(':articul', $articul, PDO::PARAM_STR);

            // Выполнение запроса
            if ($stmt->execute()) {
                http_response_code(200);
            } else {
                array_push($arr, ['error' => "Error adding data to the table: $brand/$articul"]);
            }
        } else {
            array_push($arr, ['error' => "Failed to save file: $brand/$articul"]);
        }
    }

    // Фиксация изменений
    $pdo->commit();
} catch (Exception $e) {
    // Откат изменений в случае ошибки
    $pdo->rollBack();
    array_push($arr, ['error' => "Error processing files: " . $e->getMessage()]);
}

echo json_encode($arr);
