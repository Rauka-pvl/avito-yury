<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<script>
    // JSON-объект для отправки
    var dataToSend = [{
        brand: "<?= $_GET['brand'] ?>",
        article: "<?= $_GET['article'] ?>"
    }];

    // Отправка POST-запроса с использованием jQuery
    $.ajax({
        type: 'POST',
        url: 'https://233204.fornex.cloud/multifinderbrands.php',
        // contentType: 'application/json',
        data: JSON.stringify(dataToSend),
        success: function (response) {
            console.log('Успешный ответ: ', response);
        },
        error: function (error) {
            console.error('Ошибка: ', error);
        }
    });

</script>

<body>

</body>

</html>