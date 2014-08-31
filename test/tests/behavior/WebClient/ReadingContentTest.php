<?php

namespace pjdietz\RestCms\Test\Behavior\WebClient;

use pjdietz\RestCms\Content\ContentReader;
use pjdietz\RestCms\Test\TestCases\DatabaseTestCase;

class ReadingContentTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathProvider
     */
    public function testReadByPath($path)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader("stdClass");
        $content = $reader->readWithPath($db, $path);
        $this->assertEquals($path, $content->path);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::invalidPathProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testFailToReadByPath($path)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader("stdClass");
        $content = $reader->readWithPath($db, $path);
        $this->assertEmpty($content);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ContentProvider::validPathAndLocaleProvider
     */
    public function testReadByPathAndLocale($path, $locale, $expectedLocale)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ContentReader("stdClass");
        $content = $reader->readWithPath($db, $path, $locale);
        $this->assertEquals($expectedLocale, $content->locale);
    }
}
