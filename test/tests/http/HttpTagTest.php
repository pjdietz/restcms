<?php

namespace pjdietz\RestCms\Test;

use pjdietz\WellRESTed\Client;

class HttpTagTest extends HttpTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\TagProvider::validTagProvider
     */
    public function testReadById($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/tags/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\TagProvider::validTagProvider
     */
    public function testReadBySlug($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/tags/" . $slug);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\TagProvider::invalidTagProvider
     */
    public function testReadMissing($id)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/tags/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(404, $resp->getStatusCode());
    }
}
