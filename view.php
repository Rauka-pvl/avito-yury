<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: index.php');
    exit;
}

require_once 'db/db.php';
$offset = $_GET['page'] ?? 1;
$page = $_GET['page'] ?? 1;
$sort = $_GET['sort'] ?? 'ASC';
$search = $_GET['search'] ?? '';
$searchA = $_GET['searchA'] ?? '';
if ($offset != 1)
    $offset = $_GET['page'] * 100;

if ($search && $searchA) {
    $sql = "SELECT * FROM images WHERE brand LIKE CONCAT('%', :search ,'%') AND articul LIKE CONCAT('%', :searchA ,'%') ORDER BY brand $sort LIMIT 100 OFFSET $page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $stmt->bindParam(':searchA', $searchA, PDO::PARAM_STR);

    $sql2 = "SELECT COUNT(*) as count FROM images WHERE brand LIKE CONCAT('%', :search ,'%') AND articul LIKE CONCAT('%', :searchA ,'%') ORDER BY brand $sort";
    $count = $pdo->prepare($sql2);
    $count->bindParam(':search', $search, PDO::PARAM_STR);
    $count->bindParam(':searchA', $searchA, PDO::PARAM_STR);
} else if ($search) {
    $sql = "SELECT * FROM images WHERE brand LIKE CONCAT('%', :search ,'%') ORDER BY brand $sort LIMIT 100 OFFSET $page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);

    $sql2 = "SELECT COUNT(*) as count FROM images WHERE brand LIKE CONCAT('%', :search ,'%') ORDER BY brand $sort";
    $count = $pdo->prepare($sql2);
    $count->bindParam(':search', $search, PDO::PARAM_STR);
} else if ($searchA) {
    $sql = "SELECT * FROM images WHERE articul LIKE CONCAT('%', :searchA ,'%') ORDER BY brand $sort LIMIT 100 OFFSET $page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':searchA', $searchA, PDO::PARAM_STR);

    $sql2 = "SELECT COUNT(*) as count FROM images WHERE articul LIKE CONCAT('%', :searchA ,'%') ORDER BY brand $sort";
    $count = $pdo->prepare($sql2);
    $count->bindParam(':searchA', $searchA, PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM images ORDER BY brand $sort LIMIT 100 OFFSET $page";
    $stmt = $pdo->prepare($sql);

    $sql2 = "SELECT COUNT(*) as count FROM images ORDER BY brand $sort";
    $count = $pdo->prepare($sql2);
}

$stmt->execute();
$result = $stmt->fetchAll();

$count->execute();
$count = $count->fetch();

if ($count['count'] > 0)
    $pageCount = ceil($count['count'] / 100);
else
    $pageCount = 1;
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

        .pagination {
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }

        .pagination-link {
            padding: 5px 10px;
            /* background-color: #007bff; */
            /* color: #fff; */
            color: black;
            text-decoration: none;
            border-radius: 5px;
            margin: 0 5px;
        }

        .pagination-link:hover {
            background-color: #0056b3;
            color: #fff;
        }

        .page-active {
            color: #fff;
            border-radius: 5px;
            background-color: #007bff;
        }
    </style>
    <div class="container">
        <div class="center">
            <div style="margin: 5px auto;">
                <a class="btn btn-primary" href="home.php">Назад</a>
            </div>
            <form action="">
                <div style="width: 800px; margin: 1em auto; display: flex; justify-content: space-evenly; align-items: center;"
                    class="text-center">
                    <div style="margin: 0 0.5em;">
                        <label for="search">Поиск по Бренду</label>
                        <input type="text" id="search" placeholder="Поиск по Бренду" name="search"
                            value="<?= $search ?>" class="form-control">
                    </div>
                    <div style="margin: 0 0.5em;">
                        <label for="searchA">Поиск по Артиклу</label>
                        <input type="text" id="searchA" placeholder="Поиск по Артиклу" name="searchA"
                            value="<?= $searchA ?>" class="form-control">
                    </div>
                    <div style=" margin: 0 0.5em;">
                        <label for="sort">Сортировка по Бренду</label>
                        <select name="sort" class="form-control" id="sort">
                            <option value="ASC" <? if (isset($_GET['sort'])) {
                                if ($_GET['sort'] == 'ASC')
                                    echo 'selected';
                            } ?>>По возрастанию
                            </option>
                            <option value="DESC" <? if (isset($_GET['sort'])) {
                                if ($_GET['sort'] == 'DESC')
                                    echo 'selected';
                            } ?>>По убыванию</option>
                        </select>
                    </div>
                    <button type="sumbit" class="btn btn-primary" style="height: 3.5em;">Поиск</button>
                </div>
            </form>
            <div class="text-center" id="delete_selected" style="display: none;">
                <button onclick="delete_selected()" class="btn btn-danger" style="margin: 0 0 1em 0;">Удалить
                    выбранные</button>
            </div>
            <table id="myTable">
                <thead>
                    <tr>
                        <th><input type='checkbox' id='checkAll' class='selectRow'></th>
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
                        echo "<td><input type='checkbox' id='check' value='" . $d['id'] . "' class='selectRow'></td>";
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
            <div class="pagination">
                <? for ($i = 1; $i < $pageCount; $i++) { ?>
                    <a href="?page=<?= $i ?>&sort=<? echo $sort . "&search=" . $search . '&searchA=' . $searchA; ?>" class="pagination-link <? if ($page == $i)
                                     echo 'page-active'; ?>"><?= $i ?></a>
                <? } ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#checkAll').change(function () {
            if ($(this).is(':checked')) {
                let elementsWithIdCheck = document.querySelectorAll('#check');
                elementsWithIdCheck.forEach((check, i) => {
                    check.checked = true;
                });
            } else {
                let elementsWithIdCheck = document.querySelectorAll('#check');
                elementsWithIdCheck.forEach((check, i) => {
                    check.checked = false;
                });
            }
        });
        function delete_selected() {
            var checkBox = $('.selectRow:checked');
            var array = new Array();

            for (var i = 0; i < checkBox.length; i++) {
                if (checkBox[i].value != 'on')
                    array.push(checkBox[i].value);
            }
            if (array.length > 0) {
                $.ajax({
                    type: 'POST',
                    url: 'db/deleteS.php',
                    data: { ids: array },
                    success: function (response) {
                        if (response == '[]') {
                            window.location = "bool/delete/trueDelete.php";
                        } else {
                            response = JSON.parse(response);
                            var text = "Файлы: \n";
                            for (var i = 0; i < response.length; i++) {
                                text = text + response[i] + "\n";
                            }
                            text = text + "\n Не получилось удалить!";
                            alert(text);
                        }
                    },
                    error: function (error) {
                        console.error('Ошибка: ', error);
                    }
                });
            }

        }

        $(document).on('change', '.selectRow', function () {
            var selectedRows = $('.selectRow:checked').length;
            var btn_d = $('#delete_selected');

            if (selectedRows > 0) {
                btn_d.css('display', 'block');
            } else {
                btn_d.css('display', 'none');
            }
        });                                                                                                                                                                               // }
    </script>
</body>

</html>