<?php
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: index.php');
    exit;
}

require_once 'db/db.php';
$sql = "SELECT * FROM images";

$stmt = $pdo->prepare($sql);

$stmt->execute();
$result = $stmt->fetchAll();


$data = [];
foreach ($result as $row) {
    array_push($data, $row);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр</title>
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
    <style>
        table {
            margin: 0 auto;
        }

        th {
            width: 20%;
            text-align: center;
            border: 1px solid black;
        }

        td {
            padding: 5px;
            text-align: center;
            border: 1px solid black;
        }

        img {
            max-width: 470px;
        }
    </style>
    <div class="container">
        <div class="center">
            <div style="margin: 5px auto;" class="text-center">
                <a class="btn btn-primary" href="home.php">Назад</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Бранд</th>
                        <th>Артикул</th>
                        <th>Просмотр</th>
                        <!-- <th>Редактировать</th> -->
                        <th>Удалить</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $key => $d) {
                        echo "<tr>";
                        echo "<th>" . $d['brand'] . "</th>";
                        echo "<th>" . $d['articul'] . "</th>";
                        echo "<td><button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal$key'>Просмотр</button></td>";
                        echo "<td><button type='button' class='btn btn-danger' data-toggle='modal' data-target='#myModal3-$key'>Удалить</button></td>";
                        echo "</tr>";
                        echo "
                        <div class='modal' id='myModal$key'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                
                                    <!-- Заголовок модальной формы -->
                                    <div class='modal-header'>
                                        <h4 class='modal-title'>Просмотр</h4>
                                    </div>
                
                                    <!-- Тело модальной формы -->
                                    <div class='modal-body'>
                                        <img src='uploads/" . $d['brand'] . "/" . $d['articul'] . "'>
                                    </div>
                
                                    <!-- Подвал модальной формы -->
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Закрыть</button>
                                    </div>
                
                                </div>
                            </div>
                        </div>";
                        // echo "
                        // <div class='modal' id='myModal2-$key'>
                        //     <div class='modal-dialog'>
                        //         <div class='modal-content'>
                        //             <form method='post' action='db/edit.php'>
                        //                 <!-- Заголовок модальной формы -->
                        //                 <div class='modal-header'>
                        //                     <h4 class='modal-title'>Редактировать</h4>
                        //                 </div>
                    
                        //                 <!-- Тело модальной формы -->
                        //                 <div class='modal-body'>
                        //                     <input hidden value='" . $d['brand'] . "' name='brand'>
                        //                     <input hidden value='" . $d['articul'] . "' name='articul'>
                        //                     <input type='text' class='form-control' name='new_articul' required value='" . $d['articul'] . "' placeholder='Артикул'>
                        //                 </div>
                    
                        //                 <!-- Подвал модальной формы -->
                        //                 <div class='modal-footer'>
                        //                     <button type='button' class='btn btn-secondary' data-dismiss='modal'>Закрыть</button>
                        //                     <button type='sumbit' class='btn btn-success'>Сохранить изменения</button>
                        //                 </div>
                        //             </form>
                        //         </div>
                        //     </div>
                        // </div>";
                        echo "
                        <div class='modal' id='myModal3-$key'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <form method='post' action='db/delete.php'>
                                        <!-- Заголовок модальной формы -->
                                        <div class='modal-header'>
                                            <h4 class='modal-title'>Удаление</h4>
                                        </div>
                    
                                        <!-- Тело модальной формы -->
                                        <div class='modal-body'>
                                            <p>Вы уверены что хотите удалить?</p>
                                            <input hidden value='" . $d['brand'] . "' name='brand'>
                                        </div>
                    
                                        <!-- Подвал модальной формы -->
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Закрыть</button>
                                            <button type='sumbit' class='btn btn-success' value='" . $d['articul'] . "' name='articul'>Удалить</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>

</body>

</html>