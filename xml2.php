<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
require_once 'db/db.php';

/**
 * Выполняет запрос cURL и возвращает результат.
 *
 * @param string $url
 * @return string|false
 */
function fetchCurlData($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Ошибка cURL: ' . curl_error($ch);
        return false;
    }
    curl_close($ch);
    return $result;
}

/**
 * Обрабатывает XML файл: добавляет изображения и цены.
 *
 * @param SimpleXMLElement $xml
 * @param PDO $pdo
 */
function processXml(SimpleXMLElement $xml, PDO $pdo)
{
    foreach ($xml->Ad as $ad) {
        $adId = (string) $ad->Id;
        $adId = explode('_', $adId);

        // Получение изображений из базы данных
        $stmt = $pdo->prepare("SELECT * FROM images WHERE brand = :brand AND articul LIKE CONCAT('%', :articul, '%')");
        $stmt->bindParam(':brand', $adId[0], PDO::PARAM_STR);
        $stmt->bindParam(':articul', $adId[1], PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        if ($rows) {
            unset($ad->Images->Image);
            foreach ($rows as $row) {
                $path = "https://233204.fornex.cloud/uploads/" . strtolower($row['brand']) . "/" . strtolower($row['articul']);
                $newImage = $ad->Images->addChild('Image', ' ');
                $newImage->addAttribute('url', $path);
            }
        }

        // Получение цены через API
        $brand0 = $adId[0];
        $articul0 = $adId[1];
        $url = "https://abcp50533.public.api.abcp.ru/search/articles/?userlogin=api@abcp50533&userpsw=6f42e31351bc2469f37f27a7fa7da37c&number=$articul0&brand=$brand0";
        $priceData = fetchCurlData($url);

        if ($priceData) {
            $priceData = json_decode($priceData, true);
            foreach ($priceData as $priceItem) {
                if (
                    $priceItem['distributorId'] == '1664240' &&
                    $priceItem['brand'] == $brand0 &&
                    $priceItem['number'] == $articul0
                ) {
                    if ($ad->Price && $priceItem['price']) {
                        unset($ad->Price);
                        $ad->addChild('Price', $priceItem['price']);
                        break;
                    }
                }
            }
        }
    }
}

// Обработка первой ссылки
$xml1Data = fetchCurlData('https://prdownload.nodacdn.net/dfiles/7da749ad-284074-7b2184d7/articles.xml');
$xml1 = simplexml_load_string($xml1Data);

// Обработка второй ссылки
$xml2Data = fetchCurlData('https://www.buszap.ru/get_price?p=28eb21146a7944a9abd330fbf916aa7c&FranchiseeId=9117065');
$xml2 = simplexml_load_string($xml2Data);

// Обработка XML
processXml($xml1, $pdo);
processXml($xml2, $pdo);

// Объединение файлов
foreach ($xml2->Ad as $ad) {
    $newAd = $xml1->addChild('Ad', $ad->asXML());
}

// Сохранение объединенного файла
file_put_contents('combined_articles.xml', $xml1->asXML());
echo json_encode(true);
