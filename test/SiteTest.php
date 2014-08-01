<?php

namespace pjdietz\RestCms\Test;

use PDO;

class MyGuestbookTest extends \PHPUnit_Extensions_Database_TestCase
{
    protected $db;

    public function __construct()
    {
        $dsn = getenv("DB_DSN");
        $username = getenv("DB_USERNAME");
        $password = getenv("DB_PASSWORD");
        $database = getenv("DB_DATABASE");

        $this->db = new PDO($dsn, $username, $password);

        $query = <<<SQL
CREATE DATABASE IF NOT EXISTS $database
CHARACTER SET = 'utf8';

USE $database;
SQL;
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $query = <<<SQL
DROP TABLE IF EXISTS guestbook;

CREATE TABLE IF NOT EXISTS guestbook (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    content VARCHAR(255) NOT NULL,
    user VARCHAR(255) NOT NULL,
    created DATETIME NOT NULL DEFAULT '0000-00-00'
);
SQL;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
    }

    /**
     * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        return $this->createDefaultDBConnection($this->db, 'sqlite');
    }

    /**
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        //return new \PHPUnit_Extensions_Database_DataSet_DefaultDataSet();
        return $this->createFlatXMLDataSet(__DIR__ . "/fixtures/sample.xml");
    }

    public function testFakeTest()
    {
        $query = <<<SQL
SELECT * FROM guestbook;
SQL;
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $this->assertEquals(2, $stmt->rowCount());
    }
}
