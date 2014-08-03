<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Status\Status;

class StatusTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\StatusProvider::validStatusProvider
     */
    public function testReadStatusById($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $status = Status::init($id, $db);
        $this->assertEquals($slug, $status->slug);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\StatusProvider::validStatusProvider
     */
    public function testReadStatusBySlug($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $status = Status::init($slug, $db);
        $this->assertEquals($id, $status->statusId);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\StatusProvider::invalidStatusProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testReadMissingStatus($id)
    {
        $db = $this->getConnection()->getConnection();
        Status::init($id, $db);
    }
}
