<?php
include_once(__DIR__ . '/../models/Logger.php');
session_start();

class Permission{

    private $role_id;
    private $role_permissions = []; 

    public function init($role_id, $role_permissions)
    {
        $this->role_id = $role_id;
        $this->role_permissions = $role_permissions;
        return $this;
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function getRolePermissions()
    {
        return $this->role_permissions;
    }

    public static function Add($role_id, $role_permissions, $pdo)
    {
        try{
            $stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, role_permissions) VALUES (?, ?) ON DUPLICATE KEY UPDATE role_permissions = ?");
            $result = $stmt->execute([$role_id, json_encode($role_permissions), json_encode($role_permissions)]);
            Logger::success("Права успешно заданы.");
        }catch(PDOException $e){
            Logger::error("Ошибка при выдаче прав: " . $e->getMessage());
            return false;
        }
    }

    public static function GetByRoleId($role_id, $pdo)
    {
        try{
            $stmt = $pdo->prepare("SELECT role_permissions FROM role_permissions WHERE role_id = ?");
            $stmt->execute([$role_id]);
            $result = $stmt->fetchColumn();
            if($result === false) {
                Logger::error("Права для роли с ID $role_id не найдены.");
                return [];
            }
            Logger::success("Права для роли с ID $role_id успешно получены.");
            return json_decode($result, true);
        }catch(PDOException $e){
            Logger::error("Ошибка при получении прав: " . $e->getMessage());
            return [];
        }
    }

    public static function Check($permission){
        if(!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
            Logger::error("Права доступа не установлены в сессии.");
            return false;
        }

        if(in_array($permission, $_SESSION['permissions'])) {
            Logger::success("Проверка прав: доступ к '$permission' разрешен.");
            return true;
        } else {
            Logger::error("Проверка прав: доступ к '$permission' запрещен.");
            return false;
        }
    }

    public static function Require($permission){
        if(!self::Check($permission)) {
            Logger::error("Требуемое право '$permission' не предоставлено.");
            header('Location: /public/pages/403.php');
            exit;
        }
        Logger::success("Требуемое право '$permission' предоставлено.");
    }

}