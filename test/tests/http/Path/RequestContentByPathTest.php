<?php

namespace pjdietz\RestCms\Test\Http\Path;

use pjdietz\RestCms\Test\TestCases\HttpTestCase;
use pjdietz\WellRESTed\Client;

class RequestContentByPathTest extends HttpTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathProvider
     */
    public function testRespondsWithContentGivenValidPath($path)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/paths/" . $path);
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
        $rqst->setPath("/paths/" . $path);
        if ($locale) {
            $rqst->setQuery(array("locale" => $locale));
        }
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $content = json_decode($resp->getBody());
        $this->assertEquals($expectedLocale, $content->locale);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathAndLocaleProvider
     */
    public function testRespondsWithContentOnlyGivenContentQueryParameter($path, $locale, $expectedLocale, $expectedBody)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/paths/" . $path);
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
        $rqst->setPath("/paths/" . $path);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(404, $resp->getStatusCode());
    }
}