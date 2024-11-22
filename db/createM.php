<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('max_execution_time', 3600);
ini_set('post_max_size', '2048M');
ini_set('memory_limit', '4096M');

// session_start();
// if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
//     header('Location: index.php');
//     exit;
// }
require_once 'db.php';

$fileName = $_POST['fileName'];
$brands = $_POST['brand'];
$photoSrc = $_POST['photoSrc'];
$arr = [];
$pdo->beginTransaction();

try {
    foreach ($fileName as $key => $d) {
        $brand = trim(strtolower($brands[$key]), " ");
        // $articul = trim(strtolower(str_replace(['-', '_', ' '], '', $d)));
        $articul = preg_replace('/[-_\s]+/', '', $d);
        $articul = preg_replace('/\.(?=.*\.)/', '', $articul);
        $art = trim(strtolower($articul));
        $uploadDirectory = "../uploads/" . $brand . "/";
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $uploadPath = $uploadDirectory . $art;

        // Декодируем данные в двоичный формат
        $base64 = explode(',', $photoSrc[$key]);
        $binaryData = base64_decode($base64[1]);

        // Сохраняем двоичные данные в файл
        if (file_exists($uploadPath)) {
            unlink($uploadPath);
            file_put_contents($uploadPath, $binaryData);
        } else if (file_put_contents($uploadPath, $binaryData) !== false) {
            $sql = 'INSERT INTO images (brand, articul) VALUES (:brand, :articul) ON DUPLICATE KEY UPDATE articul = :articul';
            $stmt = $pdo->prepare($sql);

            // Привязка параметров и выполнение запроса
            $stmt->bindParam(':brand', $brand, PDO::PARAM_STR);
            $stmt->bindParam(':articul', $art, PDO::PARAM_STR);

            // Выполнение запроса
            if ($stmt->execute()) {
                http_response_code(200);
            } else {
                // array_push($arr, ['error' => "Error adding data to the table: $brand/$articul"]);
                $arr[$key] = ['error' => "Error adding data to the table: $brand/$art"];
            }
        } else {
            $arr[$key] = ['error' => "Failed to save file: $brand/$art"];
            // array_push($arr, ['error' => "Failed to save file: $brand/$articul"]);
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
