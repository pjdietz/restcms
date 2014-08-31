<?php

namespace pjdietz\RestCms\Test\Behavior\Editor\Content\Item;

use pjdietz\RestCms\Test\TestCases\HttpTestCase;
use pjdietz\WellRESTed\Client;

class RequestContentTest extends HttpTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validIdProvider
     */
    public function testRequestById($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/contents/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $content = json_decode($resp->getBody());
        $this->assertEquals($slug, $content->slug);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validIdProvider
     */
    public function testRequestBySlug($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/contents/" . $slug);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $content = json_decode($resp->getBody());
        $this->assertEquals($id, $content->contentId);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::invalidIdProvider
     */
    public function testFailToRequestMissingId($id)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/contents/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(404, $resp->getStatusCode());
    }
}
