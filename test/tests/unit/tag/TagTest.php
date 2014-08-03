<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Tag\Tag;

class TagTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\TagProvider::validTagProvider
     */
    public function testReadTagById($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $tag = Tag::init($id, $db);
        $this->assertEquals($slug, $tag->slug);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\TagProvider::validTagProvider
     */
    public function testReadTagBySlug($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $tag = Tag::init($slug, $db);
        $this->assertEquals($id, $tag->tagId);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\TagProvider::invalidTagProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testReadMissingTag($id)
    {
        $db = $this->getConnection()->getConnection();
        Tag::init($id, $db);
    }
}
