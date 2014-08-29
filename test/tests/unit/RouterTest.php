<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Configuration;
use pjdietz\RestCms\Handler;
use pjdietz\RestCms\Router;
use pjdietz\WellRESTed\Routes\StaticRoute;

class RouterTest extends TestCase
{
    public function testConfigurationPassesThroughToHandler()
    {
        $conf = new Configuration();
        $conf["cat"] = "Molly";

        $path = "/test/";

        $router = new Router($conf);
        $router->addRoute(new StaticRoute($path, __NAMESPACE__ . "\\RouterTestHandler"));

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));
        $mockRequest->expects($this->any())
            ->method("getPath")
            ->will($this->returnValue($path));

        $resp = $router->getResponse($mockRequest, array("id" => 2));
        $this->assertEquals($conf["cat"], $resp->getBody());
    }
}

class RouterTestHandler extends Handler
{
    public function get()
    {
        $this->response->setBody($this->configuration["cat"]);
    }
}
