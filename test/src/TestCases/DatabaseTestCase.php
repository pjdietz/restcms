<?php

namespace pjdietz\RestCms\Test\TestCases;

use PDO;
use PHPUnit_Extensions_Database_TestCase;

abstract class DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    protected $db;

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO($GLOBALS["DB_DSN"], $GLOBALS["DB_USERNAME"], $GLOBALS["DB_PASSWORD"]);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS["DB_SCHEMA"]);
        }
        return $this->conn;
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(__DIR__ . "/../../fixtures/fixtures.yml");
    }
}
