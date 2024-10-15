<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0); // Отключаем ограничение по времени

require_once 'db/db.php';

function fetchDataWithRetry($url, $retries = 3)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);
    $attempts = 0;

    while (curl_errno($ch) && $attempts < $retries) {
        $attempts++;
        echo "Retry attempt $attempts for URL: $url\n";
        sleep(1); // Небольшая пауза перед повтором
        $result = curl_exec($ch);
    }

    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
    }

    curl_close($ch);
    return $result;
}

$xmlResult = fetchDataWithRetry('https://prdownload.nodacdn.net/dfiles/7da749ad-284074-7b2184d7/articles.xml');
if (!$xmlResult) {
    echo "Ошибка при загрузке XML.";
    exit;
}

$xml = simplexml_load_string($xmlResult);
if (!$xml) {
    echo "Ошибка при разборе XML.";
    exit;
}

foreach ($xml->Ad as $ad) {
    $adId = (string) $ad->Id;
    $adId = explode('_', $adId);

    $stmt = $pdo->prepare("SELECT * FROM images WHERE brand = :brand AND articul LIKE CONCAT('%', :articul, '%')");
    $stmt->bindParam(':brand', $adId[0], PDO::PARAM_STR);
    $stmt->bindParam(':articul', $adId[1], PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetchAll();

    $url = "https://www.buszap.ru/search/" . $adId[0] . "/" . $adId[1];
    $html = fetchDataWithRetry($url);

    if ($html) {
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $prices = $xpath->query('//tr[@data-output-price]');
        if ($prices->length > 0) {
            $newPrice = $prices[0]->getAttribute('data-output-price'); // Новая цена
        }
    }

    if ($ad->Price && isset($newPrice)) {
        unset($ad->Price);
        $ad->addChild('Price', $newPrice);
    }

    if ($row) {
        unset($ad->Images->Image);
        foreach ($row as $r) {
            $path = "https://233204.fornex.cloud/uploads/" . strtolower($r['brand']) . "/" . strtolower($r['articul']);
            $newImage = $ad->Images->addChild('Image', ' ');
            $newImage->addAttribute('url', $path);
        }
    }
}

// Сохраняем результат в файл
file_put_contents('modified_articles.xml', $xml->asXML());

echo json_encode(true);


?>