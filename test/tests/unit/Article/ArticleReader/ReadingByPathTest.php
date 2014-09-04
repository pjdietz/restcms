<?php

namespace pjdietz\RestCms\Test\Article\ArticleReader;

use pjdietz\RestCms\Article\ArticleReader;
use pjdietz\RestCms\Test\TestCases\DatabaseTestCase;

class ReadingByPathTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validPathProvider
     */
    public function testFindsContentGivenValidPath($path)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ArticleReader("stdClass");
        $content = $reader->readWithPath($db, $path);
        $this->assertEquals($path, $content->path);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::invalidPathProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testThrowsExceptionGivenInvalidPath($path)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ArticleReader("stdClass");
        $content = $reader->readWithPath($db, $path);
        $this->assertEmpty($content);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validPathAndLocaleProvider
     */
    public function testFindsBestMatchingContentForPathAndLocale($path, $locale, $expectedLocale)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ArticleReader("stdClass");
        $content = $reader->readWithPath($db, $path, $locale);
        $this->assertEquals($expectedLocale, $content->locale);
    }
}
