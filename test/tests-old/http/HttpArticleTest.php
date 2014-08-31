<?php

namespace pjdietz\RestCms\Test;

use pjdietz\WellRESTed\Client;

class HttpArticleTest extends HttpTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validIdProvider
     */
    public function testReadById($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/articles/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validIdProvider
     */
    public function testReadBySlug($id, $slug)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/articles/" . $slug);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(200, $resp->getStatusCode());
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::invalidIdProvider
     */
    public function testReadMissing($id)
    {
        $rqst = $this->getRequest();
        $rqst->setPath("/articles/" . $id);
        $rqst->setMethod("GET");
        $client = new Client();
        $resp = $client->request($rqst);
        $this->assertEquals(404, $resp->getStatusCode());
    }
}
