<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db/db.php';

// Функция для загрузки XML
function loadXML($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Отключаем SSL-проверку
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
        curl_close($ch);
        return false;
    }
    curl_close($ch);
    return simplexml_load_string($result);
}

// Функция обработки блоков по 10 записей
function processChunk($xmlChunk, $pdo)
{
    foreach ($xmlChunk as $ad) {
        if (!is_object($ad)) {
            // Пропускаем, если это не объект XML
            continue;
        }

        if (isset($ad->Id)) {
            $adId = (string) $ad->Id;
            $adId = explode('_', $adId);

            // Получаем изображения из базы данных
            $stmt = $pdo->prepare("SELECT * FROM images WHERE brand = :brand AND articul LIKE CONCAT('%', :articul, '%')");
            $stmt->bindParam(':brand', $adId[0], PDO::PARAM_STR);
            $stmt->bindParam(':articul', $adId[1], PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetchAll();

            // Получаем цены с сайта
            $url = "https://www.buszap.ru/search/" . $adId[0] . "/" . $adId[1];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $html = curl_exec($ch);
            curl_close($ch);

            if (!$html) {
                echo "Ошибка: пустой ответ от $url\n";
                continue;
            }

            $dom = new DOMDocument;
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            $prices = $xpath->query('//tr[@data-output-price]');
            if ($prices->length > 0) {
                $newPrice = $prices[0]->getAttribute('data-output-price');
            }

            // Обновляем цену в XML
            if ($ad->Price && isset($newPrice)) {
                unset($ad->Price);
                $newP = $ad->addChild('Price', $newPrice);
            }

            // Обновляем изображения в XML
            if ($row) {
                unset($ad->Images->Image);
                foreach ($row as $r) {
                    $path = "https://233204.fornex.cloud/uploads/" . strtolower($r['brand']) . "/" . strtolower($r['articul']);
                    $newImage = $ad->Images->addChild('Image', ' ');
                    $newImage->addAttribute('url', $path);
                }
            }
        }
    }
}

// Загружаем XML с сайта
$xml = loadXML('https://prdownload.nodacdn.net/dfiles/7da749ad-284074-7b2184d7/articles.xml');
if (!$xml) {
    die('Ошибка загрузки XML.');
}

// Преобразуем SimpleXMLElement в массив
$xmlArray = (array) $xml->Ad;
$totalCount = count($xmlArray);
$limit = 10; // Обрабатываем по 10 записей
$totalChunks = ceil($totalCount / $limit); // Всего частей

// Обрабатываем блоками по 10 записей
for ($i = 1; $i <= 3; $i++) { // Задаем 3 захода
    $chunk = array_slice($xmlArray, ($i - 1) * $limit, $limit); // Извлекаем текущий блок
    processChunk($chunk, $pdo); // Обрабатываем этот блок

    // Сохраняем каждый блок в отдельный файл
    $chunkXml = new SimpleXMLElement('<Ads></Ads>');
    foreach ($chunk as $ad) {
        if (is_object($ad)) {
            $adNode = $chunkXml->addChild('Ad');
            foreach ($ad->attributes() as $attr => $value) {
                $adNode->addAttribute($attr, $value);
            }
            foreach ($ad->children() as $child) {
                $adNode->addChild($child->getName(), (string) $child);
            }
        }
    }
    $chunkXml->asXML("modified_articles_part_{$i}.xml");
    echo "Обработан блок #{$i} из {$totalChunks}.\n";
}

// Объединение всех частей в один файл
$finalXml = new SimpleXMLElement('<Ads></Ads>');
for ($i = 1; $i <= 3; $i++) {
    $chunkFile = "modified_articles_part_{$i}.xml";
    if (file_exists($chunkFile)) {
        $chunkXml = simplexml_load_file($chunkFile);
        foreach ($chunkXml->Ad as $ad) {
            $adNode = $finalXml->addChild('Ad');
            foreach ($ad->attributes() as $attr => $value) {
                $adNode->addAttribute($attr, $value);
            }
            foreach ($ad->children() as $child) {
                $adNode->addChild($child->getName(), (string) $child);
            }
        }
    }
}
$finalXml->asXML('final_modified_articles.xml');
echo "Все блоки объединены в final_modified_articles.xml.\n";

?>