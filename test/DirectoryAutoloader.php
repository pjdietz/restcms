<?php

/**
 * Allow PHP to autoload classes from a specific directory.
 *
 * Classes MUST be one class or interface per file with the name of the class or interface matching the filename
 * (minus .php).
 *
 * Classes MAY be one namespace, nested namespaces (matching the directory names), or no namespace.
 *
 * Based on the AutoLoader class by Jess Telford
 * http://jes.st/2011/phpunit-bootstrap-and-autoloading-classes/
 */
class DirectoryAutoloader
{
    /**
     * Associate array of class names as keys and file names as values.
     * @var array
     */
    static private $classMap = array();

    /**
     * @param string $path Path to the directory to recurse for PHP classes.
     * @param string $namespace Namespace of the classes to autoload.
     * @param bool $appendDirectoryToNamespace If true, directory names under $path are appended to the namespace.
     */
    public static function registerDirectory($path, $namespace = "", $appendDirectoryToNamespace = false)
    {
        // Ensure the namespace ihas a trailing delimiter or is empty.
        if ($namespace && substr($namespace, -1) !== "\\") {
            $namespace .=  "\\";
        }

        // Iterate over the items in the path provided.
        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                // Directory
                // Optionally append the directory name to the namespace and recurse.
                if ($appendDirectoryToNamespace) {
                    $namespace .= $file->getFilename() . "\\";
                }
                self::registerDirectory($file->getPathname(), $namespace);
            } elseif (substr($file->getFilename(), -4) === '.php') {
                // PHP File.
                $className = substr($file->getFilename(), 0, -4);
                self::registerClass($namespace . $className, $file->getPathname());
            }
        }
    }

    /**
     * @param string $className Name (including namespace) of the class or interface to register
     * @param string $filePath Path the the file defining the class or interface
     */
    public static function registerClass($className, $filePath)
    {
        self::$classMap[$className] = $filePath;
    }

    /**
     * @param string $className
     */
    public static function loadClass($className)
    {
        if (isset(self::$classMap[$className])) {
            /** @noinspection PhpIncludeInspection */
            require_once(self::$classMap[$className]);
        }
    }

}

spl_autoload_register(array('DirectoryAutoloader', 'loadClass'));
