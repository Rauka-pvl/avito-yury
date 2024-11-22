<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'db/db.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://prdownload.nodacdn.net/dfiles/7da749ad-284074-7b2184d7/articles.xml');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// Включаем поддержку SSL (если требуется)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Ошибка cURL: ' . curl_error($ch);
}
curl_close($ch);
$xml = simplexml_load_string($result);
foreach ($xml->Ad as $ad) {
    $adId = (string) $ad->Id;
    $adId = explode('_', $adId);
    $stmt = $pdo->prepare("SELECT * FROM images WHERE brand = :brand AND articul LIKE CONCAT('%', :articul, '%')");
    $stmt->bindParam(':brand', $adId[0], PDO::PARAM_STR);
    $stmt->bindParam(':articul', $adId[1], PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetchAll();

    if ($row) {
        unset($ad->Images->Image);
        foreach ($row as $r) {
            $path = "https://233204.fornex.cloud/uploads/" . strtolower($r['brand']) . "/" . strtolower($r['articul']);
            $newImage = $ad->Images->addChild('Image', ' ');
            $newImage->addAttribute('url', $path);
            // $newImage = $ad->Images->addChild('/Image');
        }
    }

    // Price
    $brand0 = $adId[0];
    $articul0 = $adId[1];
    $url = "https://abcp50533.public.api.abcp.ru/search/articles/?userlogin=api@abcp50533&userpsw=6f42e31351bc2469f37f27a7fa7da37c&number=$articul0&brand=$brand0";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    curl_close($ch);

    $html = json_decode($html);
    foreach ($html as $h) {
        $h = (array) $h;
        if ($h['distributorId'] == '1664240' && $h['brand'] == $brand0 && $h['number'] == $articul0) {
            if ($ad->Price && $h['price']) {
                unset($ad->Price);
                $ad->addChild('Price', $h['price']);
                continue;
            }
        }
    }
}
file_put_contents('modified_articles_1.xml', $xml->asXML());
echo json_encode(true);
