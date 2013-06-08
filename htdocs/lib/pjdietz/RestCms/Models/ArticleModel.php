<?php

namespace pjdietz\RestCms\Models;

class ArticleModel extends RestCmsBaseModel
{
    public $articleId;

    private function __construct()
    {
        $this->articleId = (int) $this->articleId;
    }
}
