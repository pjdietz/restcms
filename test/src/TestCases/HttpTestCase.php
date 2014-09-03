<?php

namespace pjdietz\RestCms\Test\TestCases;

use pjdietz\ShamServer\ShamServer;
use pjdietz\ShamServer\StringShamServer;
use pjdietz\WellRESTed\Request;

abstract class HttpTestCase extends TestCase
{
    /** @var ShamServer */
    protected static $server;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        if ($GLOBALS["HTTP_CREATE"] == "true") {

            $autoloadPath = __DIR__ . "/../../../vendor/autoload.php";
            $dbDsn = $GLOBALS["DB_DSN"];
            $dbUsername = $GLOBALS["DB_USERNAME"];
            $dbPassword = $GLOBALS["DB_PASSWORD"];

            $router = <<<PHP
<?php

use pjdietz\RestCms\Configuration;
use pjdietz\RestCms\Router;
use pjdietz\WellRESTed\Request;

//ini_set("display_errors", 0);
ini_set("html_errors", 0);

require_once("$autoloadPath");

\$config = new Configuration();
\$config["DB_DSN"] = "$dbDsn";
\$config["DB_USERNAME"] = "$dbUsername";
\$config["DB_PASSWORD"] = "$dbPassword";

\$router = new Router(\$config);
\$router->respond();
//\$response = \$router->getResponse(Request::getRequest());
//\$response->respond();
PHP;
            self::$server = new StringShamServer($GLOBALS["HTTP_HOSTNAME"], $GLOBALS["HTTP_PORT"], $router);
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
