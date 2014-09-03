<?php

namespace pjdietz\RestCms\Test\Unit\Article;

use PDO;
use pjdietz\RestCms\Article\ArticleByPathHandler;
use pjdietz\RestCms\Test\Mocks\PDOMock;
use pjdietz\RestCms\Test\TestCases\TestCase;
use stdClass;

class ArticleByPathHandlerTest extends TestCase
{
    public function testRespondsToOptionsRequest()
    {
        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("OPTIONS"));

        $handler = new ArticleByPathHandler();
        $resp = $handler->getResponse($mockRequest);
        $this->assertNotNull($resp);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathAndLocaleProvider
     */
    public function testRespondsToGetRequest($path, $locale, $expectedLocale)
    {
        $mockConfig = [
            "db" => new PDOMock(),
            "articleReader" => new ContentPathHandlerTestContentReader()
        ];

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));

        if ($locale) {
            $mockRequest->expects($this->any())
                ->method("getQuery")
                ->will($this->returnValue(array("locale" => $locale)));
        } else {
            $mockRequest->expects($this->any())
                ->method("getQuery")
                ->will($this->returnValue(array()));
        }

        $handler = new ArticleByPathHandler();
        $resp = $handler->getResponse($mockRequest, [
                "path" => $path,
                "configuration" => $mockConfig
            ]);
        $body = json_decode($resp->getBody());
        $this->assertEquals($path, $body->path);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathAndLocaleProvider
     */
    public function testRespondsToGetRequestForContentOnly($path, $locale, $expectedLocale)
    {
        $query = array(
            "content" => 1
        );
        if ($locale) {
            $query["locale"] = $locale;
        }

        $mockConfig = [
            "db" => new PDOMock(),
            "articleReader" => new ContentPathHandlerTestContentReader()
        ];

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));
        $mockRequest->expects($this->any())
            ->method("getQuery")
            ->will($this->returnValue($query));

        $handler = new ArticleByPathHandler();
        $resp = $handler->getResponse($mockRequest, [
                "path" => $path,
                "configuration" => $mockConfig
            ]);
        $this->assertEquals($path, $resp->getBody());
    }
}

class ContentPathHandlerTestContentReader
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


