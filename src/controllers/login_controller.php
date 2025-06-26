<?php
    include_once(__DIR__ . '/../models/DB.php');
    include_once(__DIR__ . '/../models/User.php');
    include_once(__DIR__ . '/../security/valid.php');
    include_once(__DIR__ . '/../models/Logger.php');
    include_once(__DIR__ . '/../models/Permission.php');
    include_once(__DIR__ . '/../security/PermissionMiddleware.php');


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (empty($email) || empty($password)) {

            echo "Все поля должны быть заполнены";
            exit;
        }
        if (Login($email, $password)) {
            header('Location: /index.php?message=success');
            exit;
        } else {
            header('Location: /public/pages/login_page.php?message=error');
            exit;
        }
    }

    function Login($email, $password) {
        if(Valid::ValidateEmail($email) && Valid::ValidatePassword($password)) {
            $db = DB::DBConnect();
            if($db) {
                $result = User::GetByEmail($email, $db);
                if(!$result) {
                    Logger::error("Пользователь с email $email не найден.");
                    echo "Пользователь с таким email не найден";
                    return false;
                }
                
                if($result->getStatus() == 'active') {
                    if(password_verify($password, $result->getPasswordHash())) {
                        session_start();
                        $_SESSION['user_id'] = $result->getId();
                        $_SESSION['user_email'] = $result->getEmail();
                        $_SESSION['user_role_id'] = $result->getRoleId();
                        $_SESSION['user_status'] = $result->getStatus();

                        $result->LastActive($result->getId(), $db);
                        $_SESSION['last_active_at'] = date('Y-m-d H:i:s');

                        $permissions = Permission::GetByRoleId($result->getRoleId(), $db);
                        $_SESSION['permissions'] = $permissions;
                        Logger::success("Пользователь с email $email успешно вошел в систему.");
                        return true;
                    }else{
                        Logger::error("Неверный пароль для пользователя с email $email.");
                        return false;
                    }
                }else{
                    Logger::error("Пользователь с email $email не активен.");
                    return false;
                }
            }else {
                Logger::error("Ошибка подключения к базе данных.");
                return false;
            }
        }else{
            Logger::error("Некорректный email или пароль.");
            return false;
        }
    }

    