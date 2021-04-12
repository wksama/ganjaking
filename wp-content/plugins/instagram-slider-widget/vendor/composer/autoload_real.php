<?php

// autoload_real.php @generated by Composer

<<<<<<< HEAD
class ComposerAutoloaderInit22f1abf52dd42f8d7fbbf9ce0b042ee7
=======
class ComposerAutoloaderInite97c1636e3cc8c37e5735453014d0cf5
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
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

<<<<<<< HEAD
        spl_autoload_register(array('ComposerAutoloaderInit22f1abf52dd42f8d7fbbf9ce0b042ee7', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit22f1abf52dd42f8d7fbbf9ce0b042ee7', 'loadClassLoader'));
=======
        spl_autoload_register(array('ComposerAutoloaderInite97c1636e3cc8c37e5735453014d0cf5', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInite97c1636e3cc8c37e5735453014d0cf5', 'loadClassLoader'));
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c

        $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require __DIR__ . '/autoload_static.php';

<<<<<<< HEAD
            call_user_func(\Composer\Autoload\ComposerStaticInit22f1abf52dd42f8d7fbbf9ce0b042ee7::getInitializer($loader));
=======
            call_user_func(\Composer\Autoload\ComposerStaticInite97c1636e3cc8c37e5735453014d0cf5::getInitializer($loader));
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
        } else {
            $map = require __DIR__ . '/autoload_namespaces.php';
            foreach ($map as $namespace => $path) {
                $loader->set($namespace, $path);
            }

            $map = require __DIR__ . '/autoload_psr4.php';
            foreach ($map as $namespace => $path) {
                $loader->setPsr4($namespace, $path);
            }

            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->register(true);

        return $loader;
    }
}
