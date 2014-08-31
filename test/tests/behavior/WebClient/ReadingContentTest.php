<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Content\ContentReader;

class ReadingContentTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathProvider
     */
    public function testReadByPath($path)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader();
        $args = [
            "path" => $path
        ];
        $content = $reader->readCollection($args, $db);
        $this->assertNotEmpty($content);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::invalidPathProvider
     */
    public function testFailToReadByPath($path)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader();
        $args = [
            "path" => $path
        ];
        $content = $reader->readCollection($args, $db);
        $this->assertEmpty($content);
    }
}
