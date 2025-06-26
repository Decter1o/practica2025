<?php
class Logger{
    private static $error_log_file = 'D:/OSPanel/domains/practic/logs/errors.log';
    private static $succes_log_file = 'D:/OSPanel/domains/practic/logs/app.log';

    public static function error($message, $type = 'ERROR') {
        $timestamp = date('Y-m-d H:i:s');
        $log_message = "[$timestamp] [$type] $message" . PHP_EOL;
        
        file_put_contents(self::$error_log_file, $log_message, FILE_APPEND);
    }

    public static function success($message, $type = 'SUCCESS') {
        $timestamp = date('Y-m-d H:i:s');
        $log_message = "[$timestamp] [$type] $message" . PHP_EOL;
        
        file_put_contents(self::$succes_log_file, $log_message, FILE_APPEND);
    }
}