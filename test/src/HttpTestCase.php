<?php

namespace pjdietz\RestCms\Test;

use PHPUnit_Framework_TestCase;
use pjdietz\ShamServer\ShamServer;
use pjdietz\WellRESTed\Request;

abstract class HttpTestCase extends DatabaseTestCase
{
    /** @var ShamServer */
    protected static $server;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        if ($GLOBALS["HTTP_CREATE"] == "true") {
            self::$server = new ShamServer($GLOBALS["HTTP_HOSTNAME"], $GLOBALS["HTTP_PORT"], __DIR__ . "/../router.php");
        }
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        if (isset(self::$server)) {
            self::$server->stop();
        }
    }

    final public function getRequest($path = "/", $method = "GET")
    {
        $uri = $GLOBALS["HTTP_PROTOCOL"] . "://" . $GLOBALS["HTTP_HOSTNAME"] . ":" . $GLOBALS["HTTP_PORT"];
        $rqst = new Request($uri);
        $rqst->setPath($path);
        $rqst->setMethod($method);
        return $rqst;
    }
}
