<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Site\SiteHandler;

class SiteHandlerTest extends TestCase
{
    public function testOptions()
    {
        $mockRequest = $this->getMock('\pjdietz\WellRESTed\Interfaces\RequestInterface');
        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("OPTIONS"));

        $handler = new SiteHandler();
        $resp = $handler->getResponse($mockRequest);
        $this->assertNotNull($resp);
    }

    public function testGet()
    {
        $mockConfig = $this->getMock("\\pjdietz\\RestCms\\Configuration");
        $mockConfig->expects($this->any())
            ->method('getDatabaseConnection')
            ->will($this->returnValue(null));
        $mockConfig->expects($this->any())
            ->method('getClass')
            ->with($this->equalTo("Site"))
            ->will($this->returnValue( __NAMESPACE__ . "\\SiteHandlerTestMockSite"));

        $mockRequest = $this->getMock('\pjdietz\WellRESTed\Interfaces\RequestInterface');
        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("GET"));

        $handler = new SiteHandler();
        $resp = $handler->getResponse($mockRequest, [
                "siteId" => 1,
                "configuration" => $mockConfig
            ]);
        $body = json_decode($resp->getBody());
        $this->assertEquals(1, $body->siteId);
    }
}

class SiteHandlerTestMockSite
{
    public static function init($siteId, $db)
    {
        return (object) [
            "siteId" => 1,
            "slug" => "closeenough"
        ];
    }
}
