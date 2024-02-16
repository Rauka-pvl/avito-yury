<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

require_once 'db/db.php';

$json = file_get_contents('php://input');
$json = json_decode($json);

$sql = "SELECT * FROM images WHERE brand = :brand AND articul LIKE CONCAT('%', :articul, '%')";


$stmt = $pdo->prepare($sql);
$stmt->bindParam(':brand', $json->brand, PDO::PARAM_STR);
$stmt->bindParam(':articul', $json->article, PDO::PARAM_STR);

// Выполнение запроса
$stmt->execute();
$result = $stmt->fetchAll();


$data = [];
foreach ($result as $row) {
    $url = "https://233204.fornex.cloud/" . $row['brand'] . "/" . $row['articul'];
    array_push($data, ["url" => $url]);
}
echo json_encode($data);