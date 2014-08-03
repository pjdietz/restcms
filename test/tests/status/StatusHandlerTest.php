<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Status\StatusHandler;

class StatusHandlerTest extends TestCase
{
    public function testOptions()
    {
        $mockRequest = $this->getMock('\pjdietz\WellRESTed\Interfaces\RequestInterface');
        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("OPTIONS"));

        $handler = new StatusHandler();
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
            ->with($this->equalTo("Status"))
            ->will($this->returnValue( __NAMESPACE__ . "\\StatusHandlerTestMockSite"));

        $mockRequest = $this->getMock('\pjdietz\WellRESTed\Interfaces\RequestInterface');
        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("GET"));

        $handler = new StatusHandler();
        $resp = $handler->getResponse($mockRequest, [
                "statusId" => 1,
                "configuration" => $mockConfig
            ]);
        $body = json_decode($resp->getBody());
        $this->assertEquals(1, $body->statusId);
    }
}

class StatusHandlerTestMockSite
{
    public static function init($tagId, $db)
    {
        return (object) [
            "statusId" => 1,
            "slug" => "slug"
        ];
    }
}
