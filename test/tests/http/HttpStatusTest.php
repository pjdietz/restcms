<?php

namespace pjdietz\RestCms\Test;

use pjdietz\WellRESTed\Client;

class HttpStatusTest extends HttpTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\StatusProvider::validStatusProvider
     */
    public function testReadById($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/status/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\StatusProvider::validStatusProvider
     */
    public function testReadBySlug($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/status/" . $slug);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\StatusProvider::invalidStatusProvider
     */
    public function testReadMissing($id)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/status/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(404, $resp->getStatusCode());
    }
}
