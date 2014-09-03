<?php

namespace pjdietz\RestCms\Test\Unit\Article;

use PDO;
use pjdietz\RestCms\Article\ArticleHandler;
use pjdietz\RestCms\Test\Mocks\PDOMock;
use pjdietz\RestCms\Test\TestCases\TestCase;
use stdClass;

class ArticleHandlerTest extends TestCase
{
    public function testRespondsToOptionsRequest()
    {
        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("OPTIONS"));

        $handler = new ArticleHandler();
        $resp = $handler->getResponse($mockRequest);
        $this->assertNotNull($resp);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validIdProvider
     */
    public function testRespondsToGetRequestGivenId($id, $slug)
    {
        $mockConfig = [
            "db" => new PDOMock(),
            "articleReader" => new ContentHandlerTestContentReader()
        ];

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));

        $handler = new ArticleHandler();
        $resp = $handler->getResponse($mockRequest, [
                "articleId" => $id,
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

