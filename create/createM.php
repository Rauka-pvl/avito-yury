<?php
ini_set('post_max_size', '1024M');

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
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
        </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
</head>

<body>
    <style>
        img {
            max-width: 470px;
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
        <div style="margin: 5px auto; display:flex; justify-content: space-between;">
            <a class="btn btn-primary" href="../home.php">Назад</a>
            <button class="btn btn-success" id="btn-add" style="display: none;" onclick="addData()"
                type="submit">Добавить</button>
        </div>
        <input type="file" id="folderInput" class="form-control" style="margin: 0.5em auto;" webkitdirectory directory
            multiple />
        <div class="text-center">
            <button onclick="processFiles()" class="btn btn-success" style="margin: 0.5em auto;">Обработать
                файлы</button>
            <div>
                <input type="text" class="form-control" id="all_brand" placeholder="Названия бренда для всех!">
                <button class="btn btn-success" onclick="all_brand_name()" style="margin: 0.5em auto;">Добавить название
                    бренда</button>
            </div>
        </div>

        <div id="files" style="margin: 0.5em auto;">
        </div>
    </div>
    <datalist id=" brand_list">
        <?php
        $directory = '../uploads';

        // Получить все элементы в директории
        $files = scandir($directory);

        // Исключить . и ..
        $directories = array_diff($files, ['.', '..']);

        // Вывести названия папок
        foreach ($directories as $folder) {
            if (is_dir($directory . '/' . $folder)) {
                echo "<option value='$folder'>";
            }
        }
        ?>
    </datalist>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.0/js/bootstrap.bundle.min.js">
    </script>
    <script>
        function processFiles() {
            const folderInput = document.getElementById('folderInput');
            const files = folderInput.files;
            const files_div = document.getElementById('files');

            if (files.length > 0) {
                files_div.innerHTML = '';

                for (let i = 0; i < files.length; i++) {
                    let file = files[i];

                    let div = document.createElement('div');
                    div.classList.add('d-flex');
                    div.style.justifyContent = 'space-around';
                    div.style.margin = "0.5em 0";

                    let inputA = document.createElement('input');
                    inputA.type = 'text';
                    inputA.classList.add('form-control');
                    inputA.placeholder = 'Артикул';
                    inputA.value = file.name;
                    inputA.style.maxWidth = '400px';

                    let inputB = document.createElement('input');
                    inputB.type = 'text';
                    inputB.classList.add('form-control');
                    inputB.classList.add('brand');
                    inputB.placeholder = 'Бренд';
                    inputB.style.maxWidth = '400px';
                    inputB.id = 'brand';
                    inputB.setAttribute('list', 'brand_list');

                    let viewImg = document.createElement('button');
                    viewImg.type = 'button';
                    viewImg.classList.add('btn', 'btn-primary');
                    viewImg.setAttribute('data-toggle', 'modal');
                    viewImg.setAttribute('data-target', '#myModal' + i);
                    viewImg.textContent = 'Просмотр';
                    // viewImg.onclick = function () { previewImage(i); };

                    let modal = document.createElement('div');
                    modal.classList.add('modal');
                    modal.id = 'myModal' + i;
                    modal.innerHTML = '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Просмотр</h4></div><div class="modal-body"><img src="" id="modalImage" ></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button></div></div></div>';

                    let close = document.createElement('span');
                    close.classList.add('close');
                    close.innerHTML = '&times;';
                    close.addEventListener('click', function () {
                        files_div.removeChild(div);
                    });

                    div.appendChild(inputA);
                    div.appendChild(inputB);
                    div.appendChild(viewImg);
                    div.appendChild(modal);
                    div.appendChild(close);

                    files_div.appendChild(div);

                    let modalI = document.getElementById('myModal' + i);
                    if (modalI) {
                        let modalImage = modal.querySelector('#modalImage');
                        let folderInput = document.getElementById('folderInput');
                        let filesI = folderInput.files;

                        if (filesI && filesI.length > i && filesI[i]) {
                            let file = files[i];
                            let reader = new FileReader();
                            reader.onload = function (e) {
                                modalImage.src = e.target.result;
                            };

                            reader.readAsDataURL(file);
                        }
                    }
                }
                $("#btn-add").css('display', 'block');
            } else {
                $("#btn-add").css('display', 'none');
                alert('Папка не выбрана');
            }
        }

        function addData() {
            let filesDiv = document.getElementById('files');
            let divs = filesDiv.querySelectorAll('div.d-flex');
            // let formData = new FormData();   
            let allInputsFilled = true;

            let formDataArray = [];
            const delayInterval = 1000;

            divs.forEach((div, fileIndex) => {

                let inputA = div.querySelector('input[placeholder="Артикул"]');
                let inputB = div.querySelector('input[placeholder="Бренд"]');
                let brand = inputB.value.trim();

                // || inputA.value.includes('-') || inputA.value.includes('_')
                if (inputA.value.trim() === '' || brand === '') {
                    allInputsFilled = false;
                }

                let modal = div.querySelector('.modal');
                if (modal) {
                    let modalImage = modal.querySelector('#modalImage');
                    let photoSrc = modalImage ? modalImage.src : '';

                    formDataArray.push({
                        fileName: inputA.value.trim(),
                        brand: brand,
                        photoSrc: photoSrc
                    });

                    // formData.append('fileName[]', inputA.value.trim());
                    // formData.append('brand[]', brand);
                    // formData.append('photoSrc[]', photoSrc);
                }
            });
            if (allInputsFilled) {
                async function sendFormDataWithDelay() {
                    for (let i = 0; i < formDataArray.length; i += 10) {
                        // Отправка данных для каждой группы с интервалом
                        let formDataGroup = formDataArray.slice(i, i + 10);
                        await sendFormDataToServer(formDataGroup);
                        await delay(delayInterval);
                    }
                    setTimeout(function () {
                        window.location = "../view.php";
                    }, 500);
                }

                // Запуск функции отправки данных
                sendFormDataWithDelay();
            } else {
                // \nТак же без "-" и "_".
                alert('Пожалуйста, заполните все инпуты перед добавлением.');
            }
        }

        document.getElementById('myForm').addEventListener('submit', function (event) {
            event.preventDefault();
            addData();
        });

        function sendFormDataToServer(formDataGroup) {
            let formData = new FormData();

            formDataGroup.forEach(data => {
                formData.append('fileName[]', data.fileName);
                formData.append('brand[]', data.brand);
                formData.append('photoSrc[]', data.photoSrc);
            });

            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: '../db/createM.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 3600000,
                    success: function (response) {
                        if (response == '[]') {
                            resolve();
                        } else {
                            console.log(response);
                            reject('Ошибка при отправке данных на сервер.');
                        }
                    },
                    error: function (error) {
                        console.error('Произошла ошибка при отправке данных на сервер:', error);
                        alert('Произошла ошибка при отправке данных на сервер.', error);
                        reject('Ошибка при отправке данных на сервер.');
                    }
                });
            });
        }

        // Функция для создания задержки
        function delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        function all_brand_name() {
            let input = $('#all_brand').val();
            if (input) {
                let brand_i = $('.brand');
                brand_i.each(function () {
                    if ($(this).is('input')) {
                        $(this).val(input);
                    } else {
                        $(this).text(input);
                    }
                });
            } else {
                alert('Поле не должно быть пустым!');
            }
        }
    </script>

</body>

</html>