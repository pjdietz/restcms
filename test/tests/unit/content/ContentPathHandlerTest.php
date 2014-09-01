<?php

namespace pjdietz\RestCms\Test\Unit\Content;

use PDO;
use pjdietz\RestCms\Content\ContentPathHandler;
use pjdietz\RestCms\Test\Mocks\PDOMock;
use pjdietz\RestCms\Test\TestCases\TestCase;
use stdClass;

class ContentPathHandlerTest extends TestCase
{
    public function testRespondToOptionsRequest()
    {
        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("OPTIONS"));

        $handler = new ContentPathHandler();
        $resp = $handler->getResponse($mockRequest);
        $this->assertNotNull($resp);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathAndLocaleProvider
     */
    public function testRespondForPath($path, $locale, $expectedLocale)
    {
        $mockConfig = [
            "db" => new PDOMock(),
            "contentReader" => new ContentPathHandlerTestContentReader()
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

        $handler = new ContentPathHandler();
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
    public function testRespondWithContentOnlyForPath($path, $locale, $expectedLocale)
    {
        $query = array(
            "content" => 1
        );
        if ($locale) {
            $query["locale"] = $locale;
        }

        $mockConfig = [
            "db" => new PDOMock(),
            "contentReader" => new ContentPathHandlerTestContentReader()
        ];

        $mockRequest = $this->getMock("\\pjdietz\\WellRESTed\\Interfaces\\RequestInterface");
        $mockRequest->expects($this->any())
            ->method("getMethod")
            ->will($this->returnValue("GET"));
        $mockRequest->expects($this->any())
            ->method("getQuery")
            ->will($this->returnValue($query));

        $handler = new ContentPathHandler();
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


