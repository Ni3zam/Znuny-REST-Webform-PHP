<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit33e3b234ac6199836efe5e2f28e98492
{
    public static $prefixesPsr0 = array (
        'U' => 
        array (
            'Unirest\\' => 
            array (
                0 => __DIR__ . '/..' . '/mashape/unirest-php/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit33e3b234ac6199836efe5e2f28e98492::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit33e3b234ac6199836efe5e2f28e98492::$classMap;

        }, null, ClassLoader::class);
    }
}
