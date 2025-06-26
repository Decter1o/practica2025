<?php
session_start();
include_once(__DIR__ . '/../models/Permission.php');

class PermissionMiddleware{
    public static function handle($permission){
        if(!isset($_SESSION['user_id'])){
            header('Location: /public/pages/login_page.php?error=not_logged_in');
        }

        if(!Permission::Check($permission)){
            Logger::error("Пользователь с ID {$_SESSION['user_id']} не имеет права '$permission'.");
            header('Location: /public/pages/403.php');
            exit;
        }
    }
}