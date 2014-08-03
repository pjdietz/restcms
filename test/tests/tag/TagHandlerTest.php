<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Tag\TagHandler;

class TagHandlerTest extends TestCase
{
    public function testOptions()
    {
        $mockRequest = $this->getMock('\pjdietz\WellRESTed\Interfaces\RequestInterface');
        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("OPTIONS"));

        $handler = new TagHandler();
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
            ->with($this->equalTo("Tag"))
            ->will($this->returnValue( __NAMESPACE__ . "\\TagHandlerTestMockSite"));

        $mockRequest = $this->getMock('\pjdietz\WellRESTed\Interfaces\RequestInterface');
        $mockRequest->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue("GET"));

        $handler = new TagHandler();
        $resp = $handler->getResponse($mockRequest, [
                "tagId" => 1,
                "configuration" => $mockConfig
            ]);
        $body = json_decode($resp->getBody());
        $this->assertEquals(1, $body->tagId);
    }
}

class TagHandlerTestMockSite
{
    public static function init($tagId, $db)
    {
        return (object) [
            "tagId" => 1,
            "slug" => "slug"
        ];
    }
}
