<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
include_once(__DIR__ . '/../models/Logger.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Ramsey\Uuid\Uuid;

class Secure{
    function SendVerifiLink($userEmail, $pdo) {
        $config = require(__DIR__ . '/../../config/config.php');
        $token = bin2hex(random_bytes(16));
        $id = Uuid::uuid7()->toString();
        
        $stmt = $pdo->prepare("INSERT INTO email_verification (id, email, token, expires_at) VALUES (?, ?, ?, NOW() + INTERVAL 1 DAY)");
        $stmt->execute([$id, $userEmail, $token]);
        
        $link = "http://practic:8080/src/controllers/email_verification_controller.php?token=" . $token;

        $mailer = new PHPMailer(true);

        try {
            $mailer->isSMTP();
            $mailer->Host = $config['email']['host'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $config['email']['username'];
            $mailer->Password = $config['email']['password'];
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mailer->Port = $config['email']['port'];
            $mailer->setFrom($config['email']['email'], 'Mailer');
            $mailer->addAddress($userEmail);
            $mailer->isHTML(true);
            $mailer->Subject = 'Подтверждение почты';
            $mailer->Body = "Здравствуйте!<br><br>Пожалуйста, подтвердите ваш email, перейдя по ссылке:<br><a href=\"$link\">$link</a><br><br>Ссылка активна 24 часа.";

            $mailer->send();
            return true;
        } catch (Exception $e) {
            Logger::error("Ошибка при отправке письма: {$mailer->ErrorInfo}");
            return false;
        }
    }
    function VerifyEmail($token, $pdo){
        $stmt = $pdo->prepare("SELECT * FROM email_verification WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        if($stmt->rowCount() > 0){
            $verification = $stmt->fetch();
            $email = $verification['email'];
            $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE email = ?");
            if($stmt->execute([$email])) {
                $stmt = $pdo->prepare("DELETE FROM email_verification WHERE token = ?");
                $stmt->execute([$token]);
                return true;
            }
        }
        return false;
    }
}