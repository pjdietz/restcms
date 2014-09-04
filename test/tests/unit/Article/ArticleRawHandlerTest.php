<?php

namespace pjdietz\RestCms\Test\Unit\Article;

use PDO;
use pjdietz\RestCms\Article\ArticleRawHandler;
use pjdietz\RestCms\Test\Mocks\PDOMock;
use pjdietz\RestCms\Test\TestCases\TestCase;
use stdClass;

class ArticleRawHandlerTest extends TestCase
{
    public function testRespondsToOptionsRequest()
    {
        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("OPTIONS"));

        $handler = new ArticleRawHandler();
        $resp = $handler->getResponse($mockRequest);
        $this->assertNotNull($resp);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validIdProvider
     */
    public function testRespondsToGetRequestForValidIdWith200($id, $slug)
    {
        $mockConfig = [
            "db" => new PDOMock(),
            "articleReader" => new ArticleRawHandlerTestArticleReader()
        ];

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));

        $handler = new ArticleRawHandler();
        $resp = $handler->getResponse($mockRequest, [
                "articleId" => $id,
                "configuration" => $mockConfig
            ]);
        $this->assertEquals(200, $resp->getStatusCode());
    }
}

class ArticleRawHandlerTestArticleReader
{
    public function read(PDO $db, $id)
    {
        $content = new stdClass();
        $content->content = "Content";
        $content->contentType = "text/html";
        return $content;
    }
}

