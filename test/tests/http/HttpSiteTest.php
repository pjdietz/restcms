<?php

namespace pjdietz\RestCms\Test;

use pjdietz\WellRESTed\Client;

class HttpSiteTest extends HttpTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\SiteProvider::validSiteProvider
     */
    public function testReadById($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/sites/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\SiteProvider::validSiteProvider
     */
    public function testReadBySlug($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/sites/" . $slug);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\SiteProvider::invalidSiteProvider
     */
    public function testReadMissing($id)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/sites/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(404, $resp->getStatusCode());
    }
}
