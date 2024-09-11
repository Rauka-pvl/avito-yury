<?php
$url = 'https://www.buszap.ru/search/FASE/FSE11234007';
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
    echo $prices[0]->getAttribute('data-output-price');
}
curl_close($ch);

?>