<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2b2dbaf70c7b243999d1d14345e5a511
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'I' => 
        array (
            'Iyzipay\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Iyzipay\\' => 
        array (
            0 => __DIR__ . '/..' . '/iyzico/iyzipay-php/src/Iyzipay',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2b2dbaf70c7b243999d1d14345e5a511::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2b2dbaf70c7b243999d1d14345e5a511::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2b2dbaf70c7b243999d1d14345e5a511::$classMap;

        }, null, ClassLoader::class);
    }
}
