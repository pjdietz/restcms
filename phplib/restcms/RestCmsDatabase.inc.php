<?php

namespace restcms;

require_once('restcms/config.inc.php');
//require_once('NthMySQLDatabase/NthMySQLDatabase.inc.php');

class RestCmsDatabase {

    protected static $databaseConnection;

    public static function getConnection() {

        if (!isset(self::$databaseConnection)) {

            // Create a new instance of the database and store it statically.
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8',
                \restcms\config\MYSQL_HOSTNAME,
                \restcms\config\MYSQL_DATABASE);

            self::$databaseConnection = new \PDO($dsn,
                \restcms\config\MYSQL_USERNAME,
                \restcms\config\MYSQL_PASSWORD);

            self::$databaseConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$databaseConnection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        }

        return self::$databaseConnection;

    }

}


?>
