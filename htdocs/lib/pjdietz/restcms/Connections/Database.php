<?php

namespace pjdietz\restcms\Connections;

use pjdietz\restcms\config;
use PDO;
use InvalidArgumentException;

class Database
{
    /**
     * Shared PDO singleton instance.
     *
     * @var \PDO
     */
    protected static $databaseConnection;

    /**
     * Return the PDO singleton instance, creating it if needed.
     *
     * @return \PDO
     */
    public static function getDatabaseConnection()
    {
        if (!isset(self::$databaseConnection)) {

            // Create a new instance of the database and store it statically.
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8',
                config\MYSQL_HOSTNAME,
                config\MYSQL_DATABASE);

            /* Without a database specified.
           $dsn = sprintf(
                'mysql:host=%s;charset=utf8',
                config\MYSQL_HOSTNAME);
            */

            self::$databaseConnection = new PDO($dsn,
                config\MYSQL_USERNAME,
                config\MYSQL_PASSWORD);

            self::$databaseConnection->setAttribute(
                PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$databaseConnection->setAttribute(
                PDO::ATTR_EMULATE_PREPARES, false);
        }

        return self::$databaseConnection;
    }

    public static function getQuery($query)
    {
        $pathToQuery = config\QUERIES_DIR . $query . '.sql';
        if (!file_exists($pathToQuery)) {
            throw new InvalidArgumentException('file does not exist: ' . $pathToQuery);
        }
        return file_get_contents($pathToQuery);
    }

}
