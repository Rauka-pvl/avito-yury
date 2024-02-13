<?php
session_start();

// Разрушаем сессию
session_destroy();

// Перенаправляем на страницу входа
header('Location: index.php');
exit;
?>