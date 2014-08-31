<?php

namespace pjdietz\RestCms\Test\Behavior\Editor\Content\Item;

use pjdietz\RestCms\Content\ContentReader;
use pjdietz\RestCms\Test\TestCases\DatabaseTestCase;
use stdClass;

class ReadingContentTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validIdProvider
     */
    public function testReadById($id, $slug)
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
    public function testReadBySlug($id, $slug)
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
    public function testFailToReadMissingId($id)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader("stdClass");
        /** @var stdClass $article */
        $reader->read($db, $id);
    }
}
