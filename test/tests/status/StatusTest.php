<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Status\Status;

class StatusTest extends DatabaseTestCase
{
    /**
     * @dataProvider validStatusProvider
     */
    public function testReadStatusById($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $status = Status::init($id, $db);
        $this->assertEquals($slug, $status->slug);
    }

    /**
     * @dataProvider validStatusProvider
     */
    public function testReadStatusBySlug($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $status = Status::init($slug, $db);
        $this->assertEquals($id, $status->statusId);
    }

    public function validStatusProvider()
    {
        return [
            [1, "draft"],
            [2, "published"],
            [3, "pending"],
            [4, "removed"]
        ];
    }

    /**
     * @dataProvider invalidStatusProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testReadMissingStatus($id)
    {
        $db = $this->getConnection()->getConnection();
        Status::init($id, $db);
    }

    public function invalidStatusProvider()
    {
        return [
            [6], ["birds"]
        ];
    }

}
