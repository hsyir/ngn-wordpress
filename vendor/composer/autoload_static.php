<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9fe983332e3837be611b4923bb896566
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WeDevs_Settings_API' => __DIR__ . '/..' . '/tareq1988/wordpress-settings-api-class/src/class.settings-api.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit9fe983332e3837be611b4923bb896566::$classMap;

        }, null, ClassLoader::class);
    }
}
