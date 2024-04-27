<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Подключение к базе данных (предполагается, что $pdo уже установлено)
require_once 'db.php';

$i = 0;

$uploadDirectory = "../uploads/" . trim(strtolower($_POST['brand']), " ") . "/";
$brand = trim(strtolower($_POST['brand']), " ");
foreach ($_FILES['images']['name'] as $key => $image) {
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $filename = basename($_FILES['images']['name'][$key]);
    $path = pathinfo($_FILES['images']['name'][$key])['extension'];
    $articul = preg_replace('/[-_\s]+/', '', $_POST['articul']);
    $art = preg_replace('/\.(?=.*\.)/', '', $articul);
    $artt = trim(strtolower($art));
    // $art = trim(strtolower(preg_replace('/(?<=\S)[-_]+(?!.*_\d+\.[^.]+$)|\s+/', '', $_POST['articul'])));
    if ($key != 0) {
        $i++;
        $name = $artt . "_" . $i . "." . $path;
    } else {
        $name = $artt . "." . $path;
    }
    $uploadPath = $uploadDirectory . $name;

    // Проверка наличия файла и удаление старого файла, если он существует
    if (file_exists($uploadPath)) {
        unlink($uploadPath);
        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $uploadPath)) {
            header('Location: ../create/true.php');
        }
    } else {
        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $uploadPath)) {
            $sql = 'INSERT INTO images (brand, articul) VALUES (:brand, :articul) ON DUPLICATE KEY UPDATE articul = :articul';
            $stmt = $pdo->prepare($sql);

            // Привязка параметров и выполнение запроса
            $stmt->bindParam(':brand', $brand, PDO::PARAM_STR);
            $stmt->bindParam(':articul', $name, PDO::PARAM_STR);

            // Выполнение запроса
            if ($stmt->execute()) {
                header('Location: ../create/true.php');
            } else {
                echo 'Ошибка при добавлении данных в таблицу.';
            }
        } else {
            echo 'Не удалось загрузить файл.';
        }
    }
}
?>