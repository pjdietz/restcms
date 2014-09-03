<?php

namespace pjdietz\RestCms\Test\Unit;

use pjdietz\RestCms\Configuration;
use pjdietz\RestCms\Test\TestCases\TestCase;

class ConfigurationTest extends TestCase
{
    public function testProvidesArticleReader()
    {
        $conf = new Configuration();
        $reader = $conf["articleReader"];
        $this->assertNotNull($reader);
    }

    public function testProvidesDatabaseConnection()
    {
        $conf = new Configuration();
        $conf["DB_DSN"] = $GLOBALS["DB_DSN"];
        $conf["DB_USERNAME"] = $GLOBALS["DB_USERNAME"];
        $conf["DB_PASSWORD"] = $GLOBALS["DB_PASSWORD"];
        $db = $conf["db"];
        $this->assertNotNull($db);
    }
}
