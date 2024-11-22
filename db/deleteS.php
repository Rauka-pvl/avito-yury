<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once 'db.php';
$res = [];
if (isset($_POST['ids'])) {
    foreach ($_POST['ids'] as $ids) {
        $id = $ids;
        // Запрос для получения информации из базы данных по ID
        $selectQuery = "SELECT * FROM images WHERE id = :id";
        $stmtSelect = $pdo->prepare($selectQuery);
        $stmtSelect->bindParam(':id', $id);
        $stmtSelect->execute();
        $fileInfo = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        // Если информация найдена, удаляем запись из базы данных и сам файл
        if ($fileInfo) {
            $deleteQuery = "DELETE FROM images WHERE id = :id";
            $stmtDelete = $pdo->prepare($deleteQuery);
            $stmtDelete->bindParam(':id', $id);
            $stmtDelete->execute();

            $filePath = '../uploads/' . $fileInfo['brand'] . '/' . $fileInfo['articul'];

            if (file_exists($filePath)) {
                unlink($filePath);
                // echo "Файл и запись успешно удалены с сервера.";
                // header('Location: ../bool/delete/trueDelete.php');
            } else {
                // echo "Файл не найден или не может быть удален.";
                // header('Location: ../bool/delete/falseDeleteFile.php');
                array_push($res, "F: $filePath");
            }
        } else {
            // echo "Запись с таким ID не найдена в базе данных.";
            // header('Location: ../bool/delete/falseDeleteDB.php');
            array_push($res, "DB: ошибка БД");
        }
    }
    $pdo = null;
    echo json_encode($res);
}
