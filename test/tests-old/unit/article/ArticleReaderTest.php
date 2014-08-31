<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Article\ArticleReader;
use stdClass;

class ArticleReaderTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validIdProvider
     */
    public function testReadById($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ArticleReader();
        /** @var stdClass $article */
        $article = $reader->read($id, $db);
        $this->assertEquals($slug, $article->slug);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validIdProvider
     */
    public function testReadBySlug($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ArticleReader();
        /** @var stdClass $article */
        $article = $reader->read($slug, $db);
        $this->assertEquals($id, $article->articleId);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::invalidIdProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testReadMissingSite($id)
    {
        $db = $this->getConnection()->getConnection();
        $reader = new ArticleReader();
        /** @var stdClass $article */
        $reader->read($id, $db);
    }
}
