<?php

namespace pjdietz\RestCms\Test\Http;;

use pjdietz\RestCms\Test\TestCases\HttpTestCase;
use pjdietz\WellRESTed\Client;

class RequestContentTest extends HttpTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathProvider
     */
    public function testRequestByPath($path)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/paths/" . $path);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::invalidPathProvider
     */
    public function testFailToRequestByPath($path)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/paths/" . $path);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(404, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathAndLocaleProvider
     */
    public function testRequestByPathAndLocale($path, $locale, $expectedLocale)
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
}
