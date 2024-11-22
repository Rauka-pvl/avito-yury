<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../index.php');
    exit;
}

require_once 'db.php';

if ($_POST['articul'] && $_POST['brand'] && $_POST['new_articul']) {
    $newFilename = $_POST['new_articul'];

    // Проверка наличия файла с новым именем
    $newFilePath = 'uploads/' . $_POST['brand'] . '/' . $newFilename;
    if (file_exists($newFilePath)) {
        echo "Файл с именем $newFilename уже существует.";
        header('Location: ../bool/edit/falseEditFile.php');
    } else {
        // Запрос для получения старого имени файла из базы данных
        $selectQuery = "SELECT * FROM images WHERE brand = :brand AND articul = :articul";
        $stmtSelect = $pdo->prepare($selectQuery);
        $stmtSelect->bindParam(':brand', $_POST['brand']);
        $stmtSelect->bindParam(':articul', $_POST['articul']);
        $stmtSelect->execute();
        $oldFileInfo = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        // Если имя файла найдено, обновляем запись в базе данных и переименовываем файл
        if ($oldFileInfo) {
            $updateQuery = "UPDATE images SET articul = :new_filename WHERE brand = :brand AND articul = :articul";
            $stmtUpdate = $pdo->prepare($updateQuery);
            $stmtUpdate->bindParam(':new_filename', $newFilename);
            $stmtUpdate->bindParam(':brand', $_POST['brand']);
            $stmtUpdate->bindParam(':articul', $_POST['articul']);
            $stmtUpdate->execute();

            // Переименование файла
            $oldFilePath = '../uploads/' . $_POST['brand'] . '/' . $oldFileInfo['articul'];
            $newFilePath = '../uploads/' . $_POST['brand'] . '/' . $newFilename;

            if (rename($oldFilePath, $newFilePath)) {
                echo "Файл успешно переименован и обновлен в базе данных.";
                header('Location: ../bool/edit/trueEdit.php');
            } else {
                echo "Не удалось переименовать файл.";
                header('Location: ../bool/edit/falseEditRenameFile.php');
            }
        } else {
            echo "Файл не найден в базе данных.";
            header('Location: ../bool/edit/falseEditNotFoundDB.php');
        }
    }
    $pdo = null;
}
