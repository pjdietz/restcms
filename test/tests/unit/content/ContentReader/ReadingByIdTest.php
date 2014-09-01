<?php

namespace pjdietz\RestCms\Test\Content;

use pjdietz\RestCms\Content\ContentReader;
use pjdietz\RestCms\Test\TestCases\DatabaseTestCase;
use stdClass;

class ReadingByIdTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validIdProvider
     */
    public function testFindsContentGivenValidId($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader("stdClass");
        /** @var stdClass $content */
        $content = $reader->read($db, $id);
        $this->assertEquals($slug, $content->slug);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validIdProvider
     */
    public function testFindsContentGivenValidSlug($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader("stdClass");
        /** @var stdClass $article */
        $content = $reader->read($db, $slug);
        $this->assertEquals($id, $content->contentId);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::invalidIdProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testThrowsExceptionsGivenInvalidId($id)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader("stdClass");
        /** @var stdClass $article */
        $reader->read($db, $id);
    }
}
