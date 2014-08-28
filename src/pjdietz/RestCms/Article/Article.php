<?php

namespace pjdietz\RestCms\Article;

use pjdietz\RestCms\Model;

/**
 * Represents an article
 */
class Article extends Model implements ArticleInterface
{
    public $articleId;
    public $slug;

    /**
     * Allow the instance to update its members after construction or deserialization.
     * @return void
     */
    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
    }
}
