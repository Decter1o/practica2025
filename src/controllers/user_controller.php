<?php
include_once(__DIR__ . '/../models/DB.php');
include_once(__DIR__ . '/../models/User.php');
include_once(__DIR__ . '/../models/Permission.php');
Permission::Check('users_control');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['id'] ?? '';
    $db = DB::DBConnect();

    switch($action) {
        case 'update':
            $email = $_POST['email'] ?? '';
            $status = $_POST['status'] ?? '';
            $role_id = $_POST['role_id'] ?? '';
            
            if (User::Update($user_id, $email, $status, $role_id, $db)) {
                header('Location: /public/pages/user_control_page.php?message=updated');
            } else {
                header('Location: /public/pages/user_control_page.php?error=update_failed');
            }
            exit;
            
        case 'delete':
            if(User::Delete($user_id, $db)) {
                header('Location: /public/pages/user_control_page.php?message=deleted');
            } else {
                header('Location: /public/pages/user_control_page.php?error=delete_failed');
            }
            exit;

        case 'restore':
            if(User::Restore($user_id, $db)) {
                header('Location: /public/pages/user_control_page.php?message=restored');
            } else {
                header('Location: /public/pages/user_control_page.php?error=restore_failed');
            }
        default:
            header('Location: /public/pages/user_control_page.php');
            exit;
    }
}