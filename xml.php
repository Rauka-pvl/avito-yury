<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 3600);
ini_set('post_max_size', '2048M');
ini_set('memory_limit', '4096M');
require_once 'db/db.php';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://prdownload.nodacdn.net/dfiles/7da749ad-284074-7b2184d7/articles.xml');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Ошибка cURL: ' . curl_error($ch);
}
curl_close($ch);

$xml = simplexml_load_string($result);
$ads = $xml->Ad;
$totalAds = count($ads);
$batchSize = 10; // количество записей для обработки за один запуск
$batchIndex = isset($_GET['batch']) ? (int) $_GET['batch'] : 0;

$start = $batchIndex * $batchSize;
$end = min($start + $batchSize, $totalAds);

for ($i = $start; $i < $end; $i++) {
    $ad = $ads[$i];
    $adId = (string) $ad->Id;
    $adId = explode('_', $adId);

    // Выполнение SQL запроса
    $stmt = $pdo->prepare("SELECT * FROM images WHERE brand = :brand AND articul LIKE CONCAT('%', :articul, '%')");
    $stmt->bindParam(':brand', $adId[0], PDO::PARAM_STR);
    $stmt->bindParam(':articul', $adId[1], PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetchAll();

    // Получение цены
    $url = "https://www.buszap.ru/search/" . $adId[0] . "/" . $adId[1];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $prices = $xpath->query('//tr[@data-output-price]');
        if ($prices->length > 0) {
            $newPrice = $prices[0]->getAttribute('data-output-price'); // Новая цена
        }
    }
    curl_close($ch);

    if ($ad->Price && isset($newPrice)) {
        unset($ad->Price);
        $newP = $ad->addChild('Price', $newPrice);
    }

    // Добавление изображений
    if ($row) {
        unset($ad->Images->Image);
        foreach ($row as $r) {
            $path = "https://233204.fornex.cloud/uploads/" . strtolower($r['brand']) . "/" . strtolower($r['articul']);
            $newImage = $ad->Images->addChild('Image', ' ');
            $newImage->addAttribute('url', $path);
        }
    }
}

// Сохранение результатов
file_put_contents('modified_articles.xml', $xml->asXML());

// Проверка на следующую партию
if ($end < $totalAds) {
    // Перенаправляем на следующую партию
    header("Location: /xml.php?batch=" . ($batchIndex + 1));
    exit;
} else {
    echo json_encode(true); // Готово
}


?>