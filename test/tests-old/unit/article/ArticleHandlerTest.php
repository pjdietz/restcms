<?php

namespace pjdietz\RestCms\Test;

use PDO;
use pjdietz\RestCms\Article\ArticleHandler;
use pjdietz\RestCms\Test\Mocks\PDOMock;
use stdClass;

class ArticleHandlerTest extends TestCase
{
    public function testRespondToOptionsRequest()
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
     * @backupGlobals disabled
     * @backupStaticAttributes disabled
     */
    public function testRespondToGetRequest()
    {
        $mockConfig = [
            "db" => new PDOMock(),
            "articleReader" => new ArticleHandlerTestArticleReader()
        ];

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));

        $handler = new ArticleHandler();
        $resp = $handler->getResponse($mockRequest, [
                "articleId" => 1,
                "configuration" => $mockConfig
            ]);
        $body = json_decode($resp->getBody());
        $this->assertEquals(1, $body->articleId);
    }
}

class ArticleHandlerTestArticleReader
{
    public function read($id, PDO $db)
    {
        $article = new stdClass();
        $article->articleId = (int) $id;
        return $article;
    }
}
