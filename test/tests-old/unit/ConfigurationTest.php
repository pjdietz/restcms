<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Configuration;

class ConfigurationTest extends TestCase
{
    public function testGetArticleReader()
    {
        $conf = new Configuration();
        $reader = $conf["articleReader"];
        $this->assertNotNull($reader);
    }

    public function testGetDatabaseConnection()
    {
        $conf = new Configuration();
        $conf["DB_DSN"] = $GLOBALS["DB_DSN"];
        $conf["DB_USERNAME"] = $GLOBALS["DB_USERNAME"];
        $conf["DB_PASSWORD"] = $GLOBALS["DB_PASSWORD"];
        $db = $conf["db"];
        $this->assertNotNull($db);
    }
}
