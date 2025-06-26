<?php

include_once (__DIR__ . '/../models/Role.php');
include_once(__DIR__ . '/../models/DB.php');
require_once(__DIR__ . '/../../vendor/autoload.php');
include_once(__DIR__ . '/../models/Permission.php');
Permission::Check('roles_control');
use Ramsey\Uuid\Uuid;


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? '';
    $db = DB::DBConnect();

    switch($action) {
        case 'create':
            $id = Uuid::uuid7()->toString();
            $name = $_POST['name'] ?? '';
            $code = $_POST['code'] ?? '';
            $permissions = $_POST['permissions'] ?? [];

            if(Role::Create($id, $name, $code, $db) && Permission::Add($id, $permissions, $db)) {
                header('Location: /public/pages/role_control_page.php?message=created');
            } else {
                header('Location: /public/pages/role_control_page.php?error=create_failed');
            }
            exit;

        case 'update':
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $code = $_POST['code'] ?? '';
            $status = $_POST['status'] ?? '';
            $permissions = $_POST['permissions'] ?? [];

            if(Role::Update($id, $name, $code, $status, $db) && Permission::Add($id, $permissions, $db)) {
                header('Location: /public/pages/role_control_page.php?message=updated');
            } else {
                header('Location: /public/pages/role_control_page.php?error=update_failed');
            }
            exit;

        case 'delete':
            $id = $_POST['id'] ?? '';

            if(Role::Delete($id, $db)) {
                header('Location: /public/pages/role_control_page.php?message=deleted');
            } else {
                header('Location: /public/pages/role_control_page.php?error=delete_failed');
            }
            exit;

        case 'restore':
            $id = $_POST['id'] ?? '';

            if(Role::Restore($id, $db)) {
                header('Location: /public/pages/role_control_page.php?message=restored');
            } else {
                header('Location: /public/pages/role_control_page.php?error=restore_failed');
            }
            exit;
            
        default:
            header('Location: /public/pages/role_control_page.php');
            exit;
    }
}