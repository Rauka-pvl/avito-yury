<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// require_once 'db/db.php';

$json = file_get_contents('php://input');
$json = json_decode($json);

var_dump($json);

// $sql = "SELECT * FROM images WHERE brand = :brand AND articul LIKE CONCAT('%', :articul, '%')";


// $stmt = $pdo->prepare($sql);
// $stmt->bindParam(':brand', $json->brand, PDO::PARAM_STR);
// $stmt->bindParam(':articul', $json->article, PDO::PARAM_STR);

// // Выполнение запроса
// $stmt->execute();
// $result = $stmt->fetchAll();


// $data = [];
// foreach ($result as $row) {
//     $url = "https://233204.fornex.cloud/" . $row['brand'] . "/" . $row['articul'];
//     array_push($data, ["url" => $url]);
// }
// echo json_encode($data);