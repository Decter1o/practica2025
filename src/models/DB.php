<?php
class DB
{
    private static $pdo;

    public static function DBConnect()
    {
        $config = require(__DIR__ . '/../../config/config.php');
        $connection_string = "mysql:host=" . $config['db']['host'] . 
                           ";dbname=" . $config['db']['dbname'] . 
                           ";charset=" . $config['db']['charset'];

        try {
            self::$pdo = new PDO(
                $connection_string, 
                $config['db']['user'], 
                $config['db']['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            return self::$pdo;
        } catch (PDOException $e) {
            error_log("Ошибка подключения к БД: " . $e->getMessage());
            return null;
        }
    }
}