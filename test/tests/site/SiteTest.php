<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Site\Site;

class SiteTest extends DatabaseTestCase
{
    /**
     * @dataProvider validSiteProvider
     */
    public function testReadSiteById($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $site = Site::init($id, $db);
        $this->assertEquals($slug, $site->slug);
    }

    /**
     * @dataProvider validSiteProvider
     */
    public function testReadSiteBySlug($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $site = Site::init($slug, $db);
        $this->assertEquals($id, $site->siteId);
    }

    public function validSiteProvider()
    {
        return [
            [1, "cats"],
            [2, "dogs"]
        ];
    }

    /**
     * @dataProvider invalidSiteProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testReadMissingSite($id)
    {
        $db = $this->getConnection()->getConnection();
        Site::init($id, $db);
    }

    public function invalidSiteProvider()
    {
        return [
            [3], ["birds"]
        ];
    }

    /**
     * @dataProvider uriProvider
     */
    public function testMakeUri($id, $path, $uri)
    {
        $db = $this->getConnection()->getConnection();
        $site = Site::init($id, $db);
        $this->assertEquals($uri, $site->makeUri($path));
    }

    public function uriProvider()
    {
        return [
            [1, "/my/page", "http://cats.localhost/my/page"],
            [2, "/",         "http://dogs.localhost/"]
        ];
    }

}
