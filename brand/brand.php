<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: index.php');
    exit;
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../db/db.php';

$stmt = $pdo->query("SELECT DISTINCT brand FROM images");
$brands = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand</title>
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <style>
        tr {
            border-bottom: 1px solid black;
        }

        table {
            margin: 0 auto;
            width: 100%;
        }

        th {
            width: 50%;
            text-align: center;
            /* border: 1px solid black; */
        }

        td {
            padding: 5px;
            text-align: center;
            /* border: 1px solid black; */
        }

        .i-m {
            margin: 0.5em 0;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <div class="container">
        <div class="center">
            <div style="margin: 0.75em auto;"><a class="btn btn-primary" href="../home.php">Назад</a></div>
            <table>
                <thead>
                    <tr>
                        <th>Бренд</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    <? foreach ($brands as $key => $brand) {
                        echo "<tr><td>$brand</td><td>
                        <button class='btn btn-primary' data-toggle='modal' data-target='#myModal$key'>Просмотр</button>
                        <button class='btn btn-warning' data-toggle='modal' data-target='#myModal2-$key'>Редактировать</button>
                        <button class='btn btn-danger' data-toggle='modal' data-target='#myModal3-$key'>Очистить</button>
                        </td></tr>";
                        $stmt1 = $pdo->prepare("SELECT sprav FROM brand_sprav WHERE brand = :brand");
                        $stmt1->bindParam(':brand', $brand, PDO::PARAM_STR);
                        $stmt1->execute();
                        $sprav = $stmt1->fetch(PDO::FETCH_COLUMN);


                        $sp = '';
                        if (!$sprav) {
                            $sp = 'Пусто!';
                            $edit = "<div>";
                            $edit .= "<div class='d-flex'><input class='form-control i-m' type='text' placeholder='Бренд...'><span onclick='closeI(this)' class='close'>&times;</span></div>";
                            $edit .= "<button class='btn btn-primary' type='button' onclick='addI(this)'>Добавить</button>";
                            $edit .= "</div>";
                        } else {
                            $s = explode(' | ', $sprav);
                            $edit = "<div>";
                            foreach ($s as $keys => $ss) {
                                $sp .= $ss . "<br>";
                                $edit .= "<div class='d-flex'><input class='form-control i-m' type='text' placeholder='Бренд...' value='$ss'><span onclick='closeI(this)' class='close'>&times;</span></div>";
                            }
                            $edit .= "<button class='btn btn-primary' type='button' onclick='addI(this)'>Добавить</button>";
                            $edit .= "</div>";
                        }

                        echo "
                        <div class='modal' id='myModal$key'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                    
                                    <!-- Заголовок модальной формы -->
                                    <div class='modal-header'>
                                        <h4 class='modal-title'>Просмотр: $brand</h4>
                                    </div>
                    
                                    <!-- Тело модальной формы -->
                                    <div class='modal-body'>
                                        $sp
                                    </div>
                    
                                    <!-- Подвал модальной формы -->
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Закрыть</button>
                                    </div>
                    
                                </div>
                            </div>
                        </div>";
                        echo "
                        <div class='modal' id='myModal2-$key'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                    
                                    <!-- Заголовок модальной формы -->
                                    <div class='modal-header' brand='$brand'>
                                        <h4 class='modal-title'>Редактировать: $brand</h4>
                                    </div>
                    
                                    <!-- Тело модальной формы -->
                                    <div class='modal-body'>
                                        $edit
                                    </div>
                    
                                    <!-- Подвал модальной формы -->
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-success' onclick='editBrandSprav(this)'>Изменить</button>
                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Закрыть</button>
                                    </div>
                    
                                </div>
                            </div>
                        </div>";
                        echo "
                        <div class='modal' id='myModal3-$key'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                    
                                    <!-- Заголовок модальной формы -->
                                    <div class='modal-header' brand='$brand'>
                                        <h4 class='modal-title'>Очистить: $brand</h4>
                                    </div>
                    
                                    <!-- Тело модальной формы -->
                                    <div class='modal-body'>
                                        Вы уверены что хотите очистить справочник по бренду?
                                    </div>
                    
                                    <!-- Подвал модальной формы -->
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-success' onclick='clearBrandSprav(" . '"' . $brand . '"' . ")'>Очистить</button>
                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Закрыть</button>
                                    </div>
                    
                                </div>
                            </div>
                        </div>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
<script>
    function closeI(e) {
        e.parentElement.remove();
    }
    function addI(e) {
        let input = $("<div class='d-flex'><input class='form-control i-m' type='text' placeholder='Бренд...'><span onclick='closeI(this)' class='close'>&times;</span></div>");
        $(input).insertBefore($(e));
    }
    function editBrandSprav(e) {
        let modal = $(e).parent().parent();
        let brand = modal.find('.modal-header').attr('brand');
        let sprav = modal.find('.modal-body > div > div');
        let sp = '';
        let IEmpty = false;
        if (sprav.length != 0) {
            sprav.each(function (index) {
                let val = $(this).find('input').val();
                if (val !== undefined) {
                    if (val != '') {
                        sp += val;
                        if (index < sprav.length - 1) {
                            sp += ' | ';
                        }
                    } else IEmpty = true;
                }
            });
            if (!IEmpty) {
                $.ajax({
                    type: 'POST',
                    url: '../db/sprav.php',
                    data: { brand: brand, sprav: sp },
                    success: function (response) {
                        if (response = []) {
                            location.reload();
                        } else {
                            console.log(response);
                            reject('Ошибка при отправке данных на сервер. ' + response);
                        }
                    },
                    error: function (error) {
                        console.error('Произошла ошибка при отправке данных на сервер:', error);
                        alert('Произошла ошибка при отправке данных на сервер.', error);
                        reject('Ошибка при отправке данных на сервер.');
                    }
                });
            } else alert('Не оставлять пустых полей!');
        } else alert('Нужен хоть бы 1 справочник!');
    }
    function clearBrandSprav(brand) {
        $.ajax({
            type: 'POST',
            url: '../db/clearSprav.php',
            data: { brand: brand },
            success: function (response) {
                if (response = []) {
                    location.reload();
                } else {
                    console.log(response);
                    reject('Ошибка при отправке данных на сервер. ' + response);
                }
            },
            error: function (error) {
                console.error('Произошла ошибка при отправке данных на сервер:', error);
                alert('Произошла ошибка при отправке данных на сервер.', error);
                reject('Ошибка при отправке данных на сервер.');
            }
        });
    }
</script>

</html>