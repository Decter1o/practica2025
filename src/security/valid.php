<?php
class Valid {
    public static function ValidateEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public static function ValidatePassword($password) {
        if (strlen($password) >= 8 && 
            preg_match('/[A-Z]/', $password) && 
            preg_match('/[a-z]/', $password) && 
            preg_match('/[0-9]/', $password)) {
            return true;
        } else {
            return false;
        }
    }

    public static function ValidateName($name) {
        if (empty($name)) {
            return false;
        }
        return preg_match('/^[a-zA-Zа-яА-ЯёЁ\s-]+$/u', $name) && strlen($name) >= 2 && strlen($name) <= 50;
    }

    public static function ValidateLastName($last_name) {
        if (empty($last_name)) {
            return false;
        }
        return preg_match('/^[a-zA-Zа-яА-ЯёЁ\s-]+$/u', $last_name) && strlen($last_name) >= 2 && strlen($last_name) <= 50;
    }
}