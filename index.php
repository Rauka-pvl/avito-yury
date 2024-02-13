<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Проверяем логин и пароль (пример, реализуйте свою логику)
    $username = 'admin';
    $password = 'password';

    if ($_POST['username'] === $username && $_POST['password'] === $password) {
        // Авторизация успешна
        $_SESSION['authenticated'] = true;
        header('Location: home.php');
        exit;
    } else {
        // Неправильные логин или пароль
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
    <div class="container" style="margin-top: 10px;">


        <form method="post" style="display:flex; justify-content: center">
            <div>
                <h2>Авторизация</h2>

                <?php if (isset($error)): ?>
                    <p style="color: red;">
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>
                <div>
                    <div>
                        <label for="username">Логин:</label>
                    </div>
                    <input id="username" type="text" name="username" required class="form-controll">
                    <div>
                        <label for="password">Пароль:</label>
                    </div>
                    <input id="password" type="password" name="password" required class="form-controll">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" style="margin: 5px 0">Войти</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>