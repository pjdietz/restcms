<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Article\Article;

class ArticleTest extends DatabaseTestCase
{
    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validIdProvider
     */
    public function testReadById($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $article = Article::init($id, $db);
        $this->assertEquals($slug, $article->slug);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::validIdProvider
     */
    public function testReadBySlug($id, $slug)
    {
        $db = $this->getConnection()->getConnection();
        $article = Article::init($slug, $db);
        $this->assertEquals($id, $article->articleId);
    }

    /**
     * @dataProvider pjdietz\RestCms\Test\Providers\ArticleProvider::invalidIdProvider
     * @expectedException \pjdietz\WellRESTed\Exceptions\HttpExceptions\NotFoundException
     */
    public function testReadMissingSite($id)
    {
        $db = $this->getConnection()->getConnection();
        Article::init($id, $db);
    }
}
