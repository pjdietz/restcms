<?php

namespace pjdietz\RestCms\Test;

use pjdietz\RestCms\Article\Article;

class ArticleModelTest extends DatabaseTestCase
{
    /**
     * @dataProvider representationAndIdProvider
     */
    public function testCastIdAsInt($representation, $expected)
    {
        $article = new ArticleReaderTestArticle($representation);
        $this->assertEquals($article->articleId, $expected);
    }

    public function representationAndIdProvider()
    {
        return [
            [
                (object) [
                    "articleId" => "1"
                ],
                1
            ],
            [
                (object) [
                    "articleId" => 7
                ],
                7
            ],
            [
                (object) [
                    "articleId" => "1002"
                ],
                1002
            ]
        ];
    }

}

class ArticleReaderTestArticle extends Article
{
    public function __construct($representation)
    {
        parent::__construct($representation);
    }
}
