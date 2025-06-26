<?php
    include_once(__DIR__ . '/../models/DB.php');
    include_once(__DIR__ . '/../models/User.php');
    include_once(__DIR__ . '/../models/Role.php');
    include_once(__DIR__ . '/../security/valid.php');
    include_once(__DIR__ . '/../security/mail.php');
    require_once(__DIR__ . '/../../vendor/autoload.php');
    use Ramsey\Uuid\Uuid;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        
        if ( empty($email) || empty($password)) {
            echo "Все поля должны быть заполнены";
            exit;
        }
        
        Register( $email, $password);
    }

    function Register( $email, $password)
    {
        if(!Valid::ValidateEmail($email)) {
            echo "Некорректный email";
            return;
        }
        if(!Valid::ValidatePassword($password)) {
            echo "Пароль должен содержать не менее 8 символов, включая заглавные буквы, строчные буквы и цифры.";
            return;
        }
            
        $db = DB::DBCOnnect();
        if ($db) {
            $id = Uuid::uuid7()->toString();
            $role = Role::GetByName($db, 'user');
            $role_id = $role->GetId();
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            if (User::Create($id, $email, $password_hash, $role_id, $db)) {
                $secure = new Secure();
                if ($secure->SendVerifiLink($email, $db)) {
                    echo "Регистрация успешна! Проверьте ваш email для подтверждения.";
                } else {
                    echo "Ошибка отправки письма с подтверждением.";
                }
            } else {
                echo "Ошибка регистрации пользователя.";
            }
        } else {
            echo "Ошибка подключения к базе данных.";
        }
    
    }
