<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;

class ArticleContentHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $article = ArticleModel::init($this->args['articleId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', $article->contentType);
        $this->response->setBody($article->content);
    }

}
