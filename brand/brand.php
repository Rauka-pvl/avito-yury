<?php
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
                        </td></tr>";
                        $stmt1 = $pdo->prepare("SELECT sprav FROM brand_sprav WHERE brand = :brand");
                        $stmt1->bindParam(':brand', $brand, PDO::PARAM_STR);
                        $stmt1->execute();
                        $sprav = $stmt1->fetch(PDO::FETCH_COLUMN);


                        $sp = '';
                        if (!$sprav) {
                            $sp = 'Пусто!';
                            $edit = "<input type='text' placholder='Бранд...' class='form-control'>";
                        } else {
                            $s = explode('|', $sprav);
                            $edit = "<div>";
                            foreach ($s as $ss) {
                                $sp .= $ss . "<br>";
                                $edit .= "<div class='d-flex'><input class='form-control i-m' type='text' placholder='Бранд...' value='$ss'><span onclick='closeI(this)' class='close'>&times;</span></div>";
                            }

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
                                    <div class='modal-header'>
                                        <h4 class='modal-title'>Редактировать: $brand</h4>
                                    </div>
                    
                                    <!-- Тело модальной формы -->
                                    <div class='modal-body'>
                                        $edit
                                    </div>
                    
                                    <!-- Подвал модальной формы -->
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-success'>Изменить</button>
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
        console.log(e.parentElement);
        remove(e.parentElement);
    }
</script>

</html>