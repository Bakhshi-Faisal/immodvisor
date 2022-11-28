<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitd4358ffd7f22b623119a80ddde6c6944
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitd4358ffd7f22b623119a80ddde6c6944', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitd4358ffd7f22b623119a80ddde6c6944', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitd4358ffd7f22b623119a80ddde6c6944::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}