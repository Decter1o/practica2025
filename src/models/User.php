<?php
include_once(__DIR__ . '/../models/Logger.php');

class User{
    private $id;
    private $email;
    private $password_hash;
    private $status;
    private $role_id;
    private $registered_at;
    private $last_active_at;

    public function init($id, $email, $password_hash, $status, $role_id, $registered_at, $last_active_at = null) {
        $this->id = $id;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->status = $status;
        $this->role_id = $role_id;
        $this->registered_at = $registered_at;
        $this->last_active_at = $last_active_at;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPasswordHash() {
        return $this->password_hash;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getRoleId() {
        return $this->role_id;
    }

    public function getRegisteredAt() {
        return $this->registered_at;
    }

    public function getLastActiveAt() {
        return $this->last_active_at;
    }    
    
    public static function GetAll($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM users");
            Logger::success("Все пользователи успешно получены.");
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'User');
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении всех пользователей: " . $e->getMessage());
            return [];
        }
    }

    public static function Create($id, $email, $password_hash, $roleId, $pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (id, email, password_hash, role_id) VALUES (?, ?, ?, ?)");
            $result = $stmt->execute([$id, $email, $password_hash, $roleId]);
            if ($result) {
                $id = $pdo->lastInsertId();
                Logger::success("Пользователь с ID $id успешно создан.");
                return true;
            }
            return false;
        } catch (PDOException $e) {
            Logger::error("Ошибка при создании пользователя: " . $e->getMessage());
            return false;
        }
    }

    public static function Update($id, $email, $status, $roleId, $pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET email = ?, role_id = ? WHERE id = ?");
            $result = $stmt->execute([$email, $roleId, $id]);
            if ($result) {
                Logger::success("Пользователь с ID $id успешно обновлен.");
            }
            return $result;
        } catch (PDOException $e) {
            Logger::error("Ошибка при обновлении пользователя: " . $e->getMessage());
            return false;
        }
    }
    
    public static function GetByEmail($email, $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                $newUser = new User();
                Logger::success("Пользователь с email $email успешно получен.");
                return $newUser->init(
                    $user['id'],
                    $user['email'], 
                    $user['password_hash'], 
                    $user['status'], 
                    $user['role_id'], 
                    $user['registered_at'], 
                    $user['last_active_at']
                );
            }
            return null;
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении пользователя по email: " . $e->getMessage());
            return null;
        }
    }

    public static function GetById($id, $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            Logger::success("Пользователь с ID $id успешно получен.");
            return $stmt->fetchObject('User');
        } catch (PDOException $e) {
            Logger::error("Ошибка при получении пользователя по ID: " . $e->getMessage());
            return null;
        }
    }

    public static function Delete($id, $pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$id]);
            if ($result) {
                Logger::success("Пользователь с ID $id успешно удален.");
            }
            return $result;
        } catch (PDOException $e) {
            Logger::error("Ошибка при удалении пользователя: " . $e->getMessage());
            return false;
        }
    }

    public static function Restore($id, $pdo) {
        try {
            $stmt = $pdo->prepare("CALL restore_user(?)");
            $result = $stmt->execute([$id]);
            if ($result) {
                Logger::success("Пользователь с ID $id успешно восстановлен.");
            }
            return $result;
        } catch (PDOException $e) {
            Logger::error("Ошибка при восстановлении пользователя: " . $e->getMessage());
            return false;
        }
    }

    public static function LastActive($id, $pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET last_active_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$id]);
            if ($result) {
                Logger::success("Время последней активности пользователя с ID $id успешно обновлено.");
            }
            return $result;
        } catch (PDOException $e) {
            Logger::error("Ошибка при обновлении времени последней активности: " . $e->getMessage());
            return false;
        }
    }
}