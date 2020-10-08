<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4492efec94b30339c28d21373d1edfaf
{
    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'YenePay\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'YenePay\\' => 
        array (
            0 => __DIR__ . '/..' . '/yenepay/php-sdk/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'R' => 
        array (
            'Requests' => 
            array (
                0 => __DIR__ . '/..' . '/rmccue/requests/library',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4492efec94b30339c28d21373d1edfaf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4492efec94b30339c28d21373d1edfaf::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit4492efec94b30339c28d21373d1edfaf::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}