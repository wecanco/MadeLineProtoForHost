<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitccf423bc1d5f809027c8eec394b5ca30
{
    public static $files = array (
        '5255c38a0faeba867671b61dfda6d864' => __DIR__ . '/..' . '/paragonie/random_compat/lib/random.php',
        'decc78cc4436b1292c6c0d151b19445c' => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib/bootstrap.php',
        'efa3b80c61fb35e374f529ec349af098' => __DIR__ . '/../..' . '/src/BigIntegor.php',
        '7c6952916da927c1fa7fc73e564a99dc' => __DIR__ . '/../..' . '/src/Socket.php',
        '9f641fdaff2d9330ca6de95bb6458e68' => __DIR__ . '/../..' . '/src/Collectable.php',
        'f30e18abdb782bea448c5103598410f9' => __DIR__ . '/../..' . '/src/Threaded.php',
        '78374a53d80784aa0ce0d3196412ec51' => __DIR__ . '/../..' . '/src/Volatile.php',
        'ee8b9dc0d1b22a255490d056f08b1a94' => __DIR__ . '/../..' . '/src/Thread.php',
        '8071b591748c33081e1801a04d20062f' => __DIR__ . '/../..' . '/src/Worker.php',
        '3f836cae0440334cb5210fceace41b10' => __DIR__ . '/../..' . '/src/Pool.php',
    );

    public static $prefixLengthsPsr4 = array (
        'p' => 
        array (
            'phpseclib\\' => 10,
            'phpDocumentor\\Reflection\\' => 25,
        ),
        'W' => 
        array (
            'Webmozart\\Assert\\' => 17,
        ),
        'R' => 
        array (
            'Rollbar\\' => 8,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'ParagonIE\\ConstantTime\\' => 23,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
        'D' => 
        array (
            'Dotenv\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'phpseclib\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpseclib/phpseclib/phpseclib',
        ),
        'phpDocumentor\\Reflection\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpdocumentor/reflection-common/src',
            1 => __DIR__ . '/..' . '/phpdocumentor/type-resolver/src',
            2 => __DIR__ . '/..' . '/phpdocumentor/reflection-docblock/src',
        ),
        'Webmozart\\Assert\\' => 
        array (
            0 => __DIR__ . '/..' . '/webmozart/assert/src',
        ),
        'Rollbar\\' => 
        array (
            0 => __DIR__ . '/..' . '/rollbar/rollbar/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'ParagonIE\\ConstantTime\\' => 
        array (
            0 => __DIR__ . '/..' . '/paragonie/constant_time_encoding/src',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
        'Dotenv\\' => 
        array (
            0 => __DIR__ . '/..' . '/vlucas/phpdotenv/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'd' => 
        array (
            'danog\\MadelineProto\\' => 
            array (
                0 => __DIR__ . '/../..' . '/src',
            ),
            'danog\\' => 
            array (
                0 => __DIR__ . '/..' . '/danog/magicalserializer/src',
                1 => __DIR__ . '/..' . '/danog/primemodule/lib',
            ),
        ),
        'P' => 
        array (
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitccf423bc1d5f809027c8eec394b5ca30::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitccf423bc1d5f809027c8eec394b5ca30::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitccf423bc1d5f809027c8eec394b5ca30::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
