<?php
include_once(__DIR__ . '/../models/Logger.php');
include_once(__DIR__ . '/../models/Permission.php');
Permission::Check('navigation_control');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');

    $data = json_decode($json, true);
    
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        Logger::Error('Некорректный JSON: ' . json_last_error_msg());
        exit;
    }

    $result = file_put_contents(__DIR__ . '/../../public/data/menu.json', $json);

    if ($result === false) {
        Logger::Error('Ошибка при записи файла menu.json');
        exit;
    }else {
        Logger::success('Меню успешно сохранено');
        echo json_encode(['success' => true]);
        exit;
    }
}else {
    Logger::Error('Метод не поддерживается');
    exit;
}