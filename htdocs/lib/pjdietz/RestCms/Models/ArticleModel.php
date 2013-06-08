<?php

namespace pjdietz\RestCms\Models;

class ArticleModel extends RestCmsBaseModel
{
    public $articleId;

    protected function prepareInstance()
    {
        $this->articleId = (int) $this->articleId;
    }
}
