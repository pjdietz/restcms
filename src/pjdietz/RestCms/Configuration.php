<?php

namespace pjdietz\RestCms;

use PDO;

class Configuration
{
    const DB_DSN = "mysql:host=localhost;dbname=restcms";
    const DB_USERNAME = "test";
    const DB_PASSWORD = "test";
    const DB_SCHEMA = "restcms";

    public function getDatabaseConnection()
    {
        static $pdo = null;
        if ($pdo === null) {
            $pdo = new PDO(self::DB_DSN, self::DB_USERNAME, self::DB_PASSWORD);
        }
        return $pdo;
    }

    public function getClass($name)
    {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                "Site" => __NAMESPACE__ . "\\Site\\Site",
                "Status" => __NAMESPACE__ . "\\Status\\Status",
                "Tag" => __NAMESPACE__ . "\\Tag\\Tag"
            );
        }

        if (isset($classes[$name])) {
            return $classes[$name];
        }
    }
}
