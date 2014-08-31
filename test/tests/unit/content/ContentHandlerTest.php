<?php

namespace pjdietz\RestCms\Test\Unit\Content;

use PDO;
use pjdietz\RestCms\Content\ContentHandler;
use pjdietz\RestCms\Content\ContentPathHandler;
use pjdietz\RestCms\Test\Mocks\PDOMock;
use pjdietz\RestCms\Test\TestCases\TestCase;
use stdClass;

class ContentHandlerTest extends TestCase
{
    public function testRespondToOptionsRequest()
    {
        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("OPTIONS"));

        $handler = new ContentHandler();
        $resp = $handler->getResponse($mockRequest);
        $this->assertNotNull($resp);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validIdProvider
     */
    public function testRespondForPath($id, $slug)
    {
        $mockConfig = [
            "db" => new PDOMock(),
            "contentReader" => new ContentHandlerTestContentReader()
        ];

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));

        $handler = new ContentHandler();
        $resp = $handler->getResponse($mockRequest, [
                "contentId" => $id,
                "configuration" => $mockConfig
            ]);
        $content = json_decode($resp->getBody());
        $this->assertEquals($id, $content->contentId);
    }
}

class ContentHandlerTestContentReader
{
    public function read(PDO $db, $id)
    {
        $content = new stdClass();
        $content->contentId = $id;
        return $content;
    }
}

