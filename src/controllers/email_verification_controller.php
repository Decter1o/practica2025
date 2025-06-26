<?php

include_once(__DIR__ . '/../models/DB.php');
include_once(__DIR__ . '/../security/mail.php');

$token = $_GET['token'] ?? null;

if(!$token){
    exit("Отсутствует токен подтверждения.");
}

if ($token) {
    $db = DB::DBCOnnect();
    if ($db) {
        $secure = new Secure();
        if ($secure->VerifyEmail($token, $db)) {
            echo "Email успешно подтвержден!";
        } else {
            echo "Ошибка подтверждения email. Возможно, ссылка устарела или неверна.";
        }
    } else {
        echo "Ошибка подключения к базе данных.";
    }
} else {
    echo "Отсутствует токен подтверждения.";
}

