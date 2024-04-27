<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

require_once 'db/db.php';

$json = file_get_contents('php://input');
$json = json_decode($json);

$json = $json[0] ?? $json;

$stmt1 = $pdo->prepare("SELECT brand FROM brand_sprav WHERE LOWER(brand) = LOWER(:brand) OR LOWER(sprav) LIKE LOWER(CONCAT('% | ',:sprav,' | %')) OR LOWER(sprav) LIKE LOWER(CONCAT('%',:sprav,'%')) OR LOWER(sprav) = LOWER(:sprav)");
$stmt1->bindParam(':brand', $json->brand, PDO::PARAM_STR);
$stmt1->bindParam(':sprav', $json->brand, PDO::PARAM_STR);
$stmt1->execute();
$sprav = $stmt1->fetch(PDO::FETCH_COLUMN);
if ($sprav)
    $brand = $sprav;
else
    $brand = $json->brand;

$sql = "SELECT * FROM images WHERE LOWER(brand) = LOWER(:brand) AND LOWER(articul) LIKE LOWER(CONCAT(:articul, '%'))";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':brand', $brand, PDO::PARAM_STR);
$stmt->bindParam(':articul', $json->article, PDO::PARAM_STR);

// Выполнение запроса
$stmt->execute();
$result = $stmt->fetchAll();

$data = [];

// Проверяем, есть ли результаты
if (!empty($result)) {
    foreach ($result as $row) {
        $url = "https://233204.fornex.cloud/uploads/" . strtolower($row['brand']) . "/" . strtolower($row['articul']);
        $url = str_replace(' ', '%20', $url);
        // Проверяем, существует ли изображение по указанной ссылке
        $imageInfo = getimagesize($url);
        if ($imageInfo !== false) {
            array_push($data, ["url" => $url]);
        }
        // else {
        //     var_dump($imageInfo !== false);
        //     var_dump($url);
        // }
    }

    if (!empty($data)) {
        header("Content-Type: application/json");
        echo json_encode($data);
    } else {
        // Если результатов нет, возвращаем 404 Not Found
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => "Изображение не найдено"]);
    }
} else {
    // Если результатов нет, возвращаем 404 Not Found
    header("HTTP/1.1 404 Not Found");
    echo json_encode(["error" => "Изображение не найдено!"]);
}
