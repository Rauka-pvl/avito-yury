<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once 'db.php';

if ($_POST['articul'] && $_POST['brand']) {
    // Запрос для получения имени файла из базы данных
    $selectQuery = "SELECT * FROM images WHERE brand = :brand AND articul = :articul";
    $stmtSelect = $pdo->prepare($selectQuery);
    $stmtSelect->bindParam(':brand', $_POST['brand']);
    $stmtSelect->bindParam(':articul', $_POST['articul']);
    $stmtSelect->execute();
    $fileInfo = $stmtSelect->fetch(PDO::FETCH_ASSOC);

    // Если имя файла найдено, удаляем запись из базы данных и сам файл
    if ($fileInfo) {
        $deleteQuery = "DELETE FROM images WHERE brand = :brand AND articul = :articul";
        $stmtDelete = $pdo->prepare($deleteQuery);
        $stmtDelete->bindParam(':brand', $_POST['brand']);
        $stmtDelete->bindParam(':articul', $_POST['articul']);
        $stmtDelete->execute();

        $filePath = '../uploads/' . $fileInfo['brand'] . '/' . $fileInfo['articul'];
        var_dump($filePath);
        if (file_exists($filePath)) {
            unlink($filePath);
            echo "Файл успешно удален с сервера.";
            header('Location: ../view.php');
        } else {
            echo "Файл не найден или не может быть удален.";
            header('Location: ../bool/delete/falseDeleteFile.php');
        }
    } else {
        echo "Файл не найден в базе данных.";
        header('Location: ../bool/delete/falseDeleteDB.php');
    }
    $pdo = null;
}

