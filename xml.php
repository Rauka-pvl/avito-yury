<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 3600);
ini_set('post_max_size', '2048M');
ini_set('memory_limit', '4096M');
require_once 'db/db.php';

// Загрузка XML файла
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://prdownload.nodacdn.net/dfiles/7da749ad-284074-7b2184d7/articles.xml');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Ошибка cURL: ' . curl_error($ch);
}
curl_close($ch);

// Парсинг XML
$xml = simplexml_load_string($result);
$newXml = new SimpleXMLElement('<Ads/>');
$newXml->addAttribute('formatVersion', '3');
$newXml->addAttribute('target', 'Avito.ru');

foreach ($xml->Ad as $ad) {
    $adId = (string) $ad->Id;
    $adIdArray = explode('_', $adId);

    // Создаем новый элемент Ad
    $newAd = $newXml->addChild('Ad');
    $newAd->addChild('Id', $adId);

    // Используем данные из $ad или задаем значения по умолчанию
    $adStatus = isset($ad->AdStatus) ? (string) $ad->AdStatus : 'Free';
    $newAd->addChild('AdStatus', $adStatus);

    $contactMethod = isset($ad->ContactMethod) ? (string) $ad->ContactMethod : 'По телефону и в сообщениях';
    $newAd->addChild('ContactMethod', $contactMethod);

    $managerName = isset($ad->ManagerName) ? (string) $ad->ManagerName : 'Станислав';
    $newAd->addChild('ManagerName', $managerName);

    $contactPhone = isset($ad->ContactPhone) ? (string) $ad->ContactPhone : '+7 (920) 761-88-80';
    $newAd->addChild('ContactPhone', $contactPhone);

    $address = isset($ad->Address) ? (string) $ad->Address : 'Россия, Тульская область, Тула, Веневское шоссе 29А';
    $newAd->addChild('Address', $address);

    $category = isset($ad->Category) ? (string) $ad->Category : 'Запчасти и аксессуары';
    $newAd->addChild('Category', $category);

    $goodsType = isset($ad->GoodsType) ? (string) $ad->GoodsType : 'Запчасти';
    $newAd->addChild('GoodsType', $goodsType);

    $productType = isset($ad->ProductType) ? (string) $ad->ProductType : 'Для грузовиков и спецтехники';
    $newAd->addChild('ProductType', $productType);

    $sparePartType = isset($ad->SparePartType) ? (string) $ad->SparePartType : 'Двигатели и комплектующие';
    $newAd->addChild('SparePartType', $sparePartType);

    $technicSparePartType = isset($ad->TechnicSparePartType) ? (string) $ad->TechnicSparePartType : 'Система зажигания';
    $newAd->addChild('TechnicSparePartType', $technicSparePartType);

    $adType = isset($ad->AdType) ? (string) $ad->AdType : 'Товар приобретен на продажу';
    $newAd->addChild('AdType', $adType);

    $title = isset($ad->Title) ? (string) $ad->Title : 'Свеча накаливания RWD/FWD Ford Transit/Peugeot';
    $newAd->addChild('Title', $title);

    // Добавление описания с использованием данных из $ad
    $description = isset($ad->Description) ? (string) $ad->Description : <<<EOD
<![CDATA[
Бренд: {$adIdArray[0]}, артикул: {$adIdArray[1]}, Свеча накаливания RWD/FWD Ford Transit/Peugeot Boxer/Citroen Jumper EURO 4<br />
Аналоги:<br />1309471, 1569520, 5960G2, B0250202130, 100226203, 100226385, EP1682248780, ET148 385, 94832, Y548G, DG130.<br />
✅ Компания BusZap (магазин запчастей и СТО) предлагает клиентам широкий выбор запчастей для коммерческого транспорта.<br />
✅ Собственный автосервис. Работаем без выходных. Доставка по России! Отпрaвкa в регионы любoй TK, нa Ваш выбор.<br /><br />
✅ При установке в нашем автосервисе гарантия 60 дней!<br />
✅ Есть возможность продажи по безналичному расчету, цена +10%<br />
Если вы хотите получить заказ через Авито-доставку стоимость запчасти будет на 10% выше, указанной в объявлении.<br />
✅ Запчасти продаются оптом и в розницу, гарантия качества. Работаем более 15 лет, Позвоните нам и мы подберем запасные части под Ваш запрос.<br />
✅ В наличии мнoго других дeтaлей для Фopд Транзит/ Fоrd Trаnzit, Пeжo Боксep/ Реugеоt Bохer, Фиат Дукатo/ Fiat Duсаtо, смотрите в профиле.
]]>
EOD;
    $newAd->addChild('Description', $description);

    $engineSparePartType = isset($ad->EngineSparePartType) ? (string) $ad->EngineSparePartType : 'Катушка зажигания, свечи, электрика';
    $newAd->addChild('EngineSparePartType', $engineSparePartType);

    // Получение изображений из базы данных
    $stmt = $pdo->prepare("SELECT * FROM images WHERE brand = :brand AND articul LIKE CONCAT('%', :articul, '%')");
    $stmt->bindParam(':brand', $adIdArray[0], PDO::PARAM_STR);
    $stmt->bindParam(':articul', $adIdArray[1], PDO::PARAM_STR);
    $stmt->execute();
    $images = $stmt->fetchAll();

    // Добавление изображений
    $imagesElement = $newAd->addChild('Images');
    foreach ($images as $image) {
        $imageUrl = "https://233204.fornex.cloud/uploads/" . strtolower($image['brand']) . "/" . strtolower($image['articul']);
        $imageElement = $imagesElement->addChild('Image');
        $imageElement->addAttribute('url', $imageUrl);
    }

    // Установка цены
    $url = "https://www.buszap.ru/search/" . $adIdArray[0] . "/" . $adIdArray[1];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    if (!curl_errno($ch)) {
        $dom = new DOMDocument;
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $priceNodes = $xpath->query('//tr[@data-output-price]');
        if ($priceNodes->length > 0) {
            $newPrice = $priceNodes[0]->getAttribute('data-output-price');
            $newAd->addChild('Price', $newPrice);
        } else {
            $newAd->addChild('Price', 'Не указана');
        }
    }
    curl_close($ch);

    // Используем данные из $ad для остальных полей
    $newAd->addChild('Condition', isset($ad->Condition) ? (string) $ad->Condition : 'Новое');
    $newAd->addChild('OEM', isset($ad->OEM) ? (string) $ad->OEM : $adIdArray[1]);
    $newAd->addChild('Brand', isset($ad->Brand) ? (string) $ad->Brand : $adIdArray[0]);
    $newAd->addChild('Availability', isset($ad->Availability) ? (string) $ad->Availability : 'В наличии');
    $newAd->addChild('InternetCalls', isset($ad->InternetCalls) ? (string) $ad->InternetCalls : 'Нет');
}

// Сохранение модифицированного XML в файл
file_put_contents('modified_articles.xml', $newXml->asXML());

echo json_encode(true);

?>