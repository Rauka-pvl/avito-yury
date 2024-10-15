<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <div style="margin: 5px auto;" class="container text-center">
        <h2>Добро пожаловать!</h2>
        <div style="display: flex; justify-content: space-evenly;">
            <a href="create/create.php" class="btn btn-primary">Добавление</a>
            <a href="create/createM.php" class="btn btn-primary">Добавление много</a>
            <a href="brand/brand.php" class="btn btn-primary">Справочник Бранда</a>
            <a href="view.php" class="btn btn-primary">Просмотр</a>
            <button id="xmlBtn" onclick="uppdateXML()" class="btn btn-primary">Обновление XML</button>
            <a href="logout.php" class="btn btn-danger">Выйти</a>
        </div>
    </div>
</body>
<script>
    function uppdateXML() {
        $('#xmlBtn').css('background-color', 'red');
        $.ajax({
            type: 'GET',
            url: 'xml.php',
            success: function (response) {
                console.log('Успешный ответ: ', response);
                if (response) {
                    $('#xmlBtn').css('background-color', '#0d6efd');
                    alert('Обновлён!');
                }
            },
            error: function (error) {
                console.error('Ошибка: ', error);
            }
        });
    }
</script>

</html>