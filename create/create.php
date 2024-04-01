<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset ($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</head>

<body>
    <div class="container">
        <div style="margin: 5px auto;" class="text-center">
            <a class="btn btn-primary" href="../home.php">Назад</a>
        </div>
        <form action="../db/function.php" method="post" enctype="multipart/form-data">
            <label for="images" class="form-control">Выберите фотографии:</label>
            <input class="form-control" type="file" name="images[]" id="images" multiple accept="image/*">
            <input type="text" name="brand" placeholder="Бренд">
            <input type="text" name="articul" placeholder="Артикул">
            <button type="submit" class="btn btn-success">Отправить</button>
        </form>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('form').addEventListener('submit', function (event) {
            let articulInput = document.querySelector('input[name="articul"]');
            let articulValue = articulInput.value.trim();

            // Проверка на пустое значение
            if (articulValue === '') {
                event.preventDefault(); // Отменяем отправку формы
                alert('Пожалуйста, введите артикул.');
                return;
            }

            // Проверка на наличие символов "-" или "_"
            // if (articulValue.includes('-') || articulValue.includes('_')) {
            //     event.preventDefault(); // Отменяем отправку формы
            //     alert('Артикул не должен содержать символы "-" или "_".');
            //     return;
            // }

            // Если все проверки пройдены, форма будет отправлена
        });
    });
</script>

</html>