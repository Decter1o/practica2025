<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb8194cf1d59af85c8d2584f1218a9b0f
{
    public static $files = array (
        'e39a8b23c42d4e1452234d762b03835a' => __DIR__ . '/..' . '/ramsey/uuid/src/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'Ramsey\\Uuid\\' => 12,
            'Ramsey\\Collection\\' => 18,
        ),
        'P' => 
        array (
            'Petre\\Practic\\' => 14,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'B' => 
        array (
            'Brick\\Math\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Ramsey\\Uuid\\' => 
        array (
            0 => __DIR__ . '/..' . '/ramsey/uuid/src',
        ),
        'Ramsey\\Collection\\' => 
        array (
            0 => __DIR__ . '/..' . '/ramsey/collection/src',
        ),
        'Petre\\Practic\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Brick\\Math\\' => 
        array (
            0 => __DIR__ . '/..' . '/brick/math/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb8194cf1d59af85c8d2584f1218a9b0f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb8194cf1d59af85c8d2584f1218a9b0f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb8194cf1d59af85c8d2584f1218a9b0f::$classMap;

        }, null, ClassLoader::class);
    }
}
