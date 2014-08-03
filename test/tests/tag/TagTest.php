<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Tag\Tag;

class TagTest extends DatabaseTestCase
{
    /**
     * @dataProvider validTagProvider
     */
    public function testReadTagById($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $tag = Tag::init($id, $db);
        $this->assertEquals($slug, $tag->slug);
    }

    /**
     * @dataProvider validTagProvider
     */
    public function testReadTagBySlug($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $tag = Tag::init($slug, $db);
        $this->assertEquals($id, $tag->tagId);
    }

    public function validTagProvider()
    {
        return [
            [1, "cats"],
            [2, "rest"]
        ];
    }

    /**
     * @dataProvider invalidTagProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testReadMissingTag($id)
    {
        $db = $this->getConnection()->getConnection();
        Tag::init($id, $db);
    }

    public function invalidTagProvider()
    {
        return [
            [4], ["birds"]
        ];
    }

}
