<?php
include_once(__DIR__ . '/../models/Logger.php');

class Role
{
    private $id;
    private $name;
    private $code;
    private $status;

    public function init($id, $name, $code, $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->status = $status;
        return $this;
    }

    public function GetId()
    {
        return $this->id;
    }

    public function GetName()
    {
        return $this->name;
    }

    public function GetCode()
    {
        return $this->code;
    }
    public function GetStatus()
    {
        return $this->status;
    }

    public static function Create($id, $name, $code, $pdo)
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO roles (id, name, code) VALUES (?, ?, ?)");
            $stmt->execute([$id, $name, $code]);
            Logger::success("Роль с ID $id успешно создана.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при создании роли: " . $e->getMessage());
            return false;
        }
        
    }    
    
    public static function Update($id, $name, $code, $status, $pdo)
    {
        try {
            $stmt = $pdo->prepare("UPDATE roles SET name = ?, code = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $code, $status, $id]);
            Logger::success("Роль с ID $id успешно обновлена.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при обновлении роли: " . $e->getMessage());
            return false;
        }
    }

    public static function Delete($id, $pdo)
    {
        try {
            $stmt = $pdo->prepare("CALL delete_role(?)");
            $stmt->execute([$id]);
            Logger::success("Роль с ID $id успешно удалена.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при удалении роли: " . $e->getMessage());
            return false;
        }
    }

    public static function Restore($id, $pdo)
    {
        try {
            $stmt = $pdo->prepare("CALL restore_role(?)");
            $stmt->execute([$id]);
            Logger::success("Роль с ID $id успешно восстановлена.");
            return true;
        } catch (PDOException $e) {
            Logger::error("Ошибка при восстановлении роли: " . $e->getMessage());
            return false;
        }
    }

    public static function GetAll($pdo)
    {
        try {
            $stmt = $pdo->query("SELECT * FROM roles");
            Logger::success("Все роли успешно получены.");
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'Role');
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении всех ролей: " . $e->getMessage());
            return [];
        }
    }

    public static function GetById($id, $pdo )
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
            $stmt->execute([$id]);
            Logger::success("Роль с ID $id успешно получена.");
            return $stmt->fetchObject('Role');
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении роли по ID: " . $e->getMessage());
            return null;
        }
    }

    public static function GetByName($pdo, $name)
    {
        try {
            $stmt = $pdo->prepare("SELECT * FROM roles WHERE code = ?");
            $stmt->execute([$name]);
            Logger::success("Роль с именем $name успешно получена.");
            return $stmt->fetchObject('Role');
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении роли по имени: " . $e->getMessage());
            return null;
        }
    }

    public static function GetNameById($id, $pdo)
    {
        try {
            $stmt = $pdo->prepare("SELECT name FROM roles WHERE id = ?");
            $stmt->execute([$id]);
            Logger::success("Имя роли с ID $id успешно получено.");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении имени роли по ID: " . $e->getMessage());
            return null;
        }
    }
}