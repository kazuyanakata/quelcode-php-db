<?php
try {
    $db = new PDO('mysql:dbname=quelcode-php-db;host=mysql;charset=utf8', 'root', 'root');
} catch (PDOException $e) {
    header('Location: error.php');
    exit();
}
