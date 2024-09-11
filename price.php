<?php

// URL страницы
$url = 'https://www.buszap.ru/search/FASE/FSE11234007';

// Инициализация cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// Получаем HTML страницы
$html = curl_exec($ch);

// Проверяем наличие ошибок при выполнении запроса
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // Парсинг HTML с помощью DOMDocument
    $dom = new DOMDocument;
    @$dom->loadHTML($html);

    // Ищем элементы с классом resultPrice
    $xpath = new DOMXPath($dom);

    // Находим элемент с атрибутом data-output-price
    $prices = $xpath->query('//tr[@data-output-price]');

    // Выводим все найденные цены
    // foreach ($prices as $price) {
    //     // Извлекаем значение атрибута data-output-price
    //     $outputPrice = $price->getAttribute('data-output-price');
    //     echo "Цена: " . $outputPrice . " ₽\n";
    // }
    echo $prices[0]->getAttribute('data-output-price');
}

// Закрываем cURL-сессию
curl_close($ch);

?>