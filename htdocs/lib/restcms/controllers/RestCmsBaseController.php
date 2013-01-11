<?php

namespace restcms\controllers;

abstract class RestCmsBaseController {

    /**
     * The instance's main data store.
     * @var array
     */
    protected $data;

    /**
     * Shared PDO singleton instance.
     * @var \PDO
     */
    protected static $databaseConnection;


    // -------------------------------------------------------------------------
    // !Accessors

    /**
     * @param string $name
     * @return array|string
     * @throws \Exception
     */
    public function __get($name) {

        switch ($name) {
            case 'data':
                return $this->getData();
            default:
                throw new \Exception('Property ' . $name . ' does not exist.');
        }

    }

    public function getData() {
        return $this->data;
    }



    // -------------------------------------------------------------------------
    // Connections

    protected static function getDatabaseConnection() {

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
