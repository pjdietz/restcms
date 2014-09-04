<?php

namespace pjdietz\RestCms\Test\Http\Article;

use pjdietz\RestCms\Test\TestCases\HttpTestCase;
use pjdietz\WellRESTed\Client;

class RequestArticleRawByPathTest extends HttpTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathProvider
     */
    public function testRespondsWithContentGivenValidPath($path)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/articles/raw/by-path/" . $path);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathAndLocaleProvider
     */
    public function testRespondsWithBestMatchingContentGivenPathAndLocale($path, $locale, $expectedLocale, $expectedBody)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/articles/raw/by-path/" . $path);
        $query = array(
            "content" => "1"
        );
        if ($locale) {
            $query["locale"] = $locale;
        }
        $rqst->setQuery($query);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals($expectedBody, $resp->getBody());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::invalidPathProvider
     */
    public function testResponds404GivenInvalidPath($path)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/article/raw/by-path/" . $path);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(404, $resp->getStatusCode());
    }
}
