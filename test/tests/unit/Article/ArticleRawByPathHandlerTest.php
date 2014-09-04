<?php

namespace pjdietz\RestCms\Test\Unit\Article;

use PDO;
use pjdietz\RestCms\Article\ArticleRawByPathHandler;
use pjdietz\RestCms\Test\Mocks\PDOMock;
use pjdietz\RestCms\Test\TestCases\TestCase;
use stdClass;

class ArticleRawByPathHandlerTest extends TestCase
{
    public function testRespondsToOptionsRequest()
    {
        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("OPTIONS"));

        $handler = new ArticleRawByPathHandler();
        $resp = $handler->getResponse($mockRequest);
        $this->assertNotNull($resp);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validPathAndLocaleProvider
     */
    public function testRespondsToGetRequest($path, $locale, $expectedLocale)
    {
        $query = array(
            "content" => 1
        );
        if ($locale) {
            $query["locale"] = $locale;
        }

        $mockConfig = [
            "db" => new PDOMock(),
            "articleReader" => new ArticleRawByPathHandlerTestArticleReader()
        ];

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));
        $mockRequest->expects($this->any())
            ->method("getQuery")
            ->will($this->returnValue($query));

        $handler = new ArticleRawByPathHandler();
        $resp = $handler->getResponse($mockRequest, [
                "path" => $path,
                "configuration" => $mockConfig
            ]);
        $this->assertEquals($path, $resp->getBody());
    }
}

class ArticleRawByPathHandlerTestArticleReader
{
    public function readWithPath(PDO $db, $path)
    {
        $content = new stdClass();
        $content->path = $path;
        $content->content = $path;
        $content->contentType = "text/html";
        return $content;
    }
}


