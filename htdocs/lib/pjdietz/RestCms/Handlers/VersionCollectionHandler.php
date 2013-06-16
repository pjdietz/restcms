<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\VersionModel;

class VersionCollectionHandler extends RestCmsBaseHandler
{

    protected function getAllowedMethods()
    {
        return array('GET', 'POST');
    }

    protected function get()
    {
        // Ensure the user may modify this article.
        $article = ArticleModel::initWithId($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        $collection = VersionModel::initCollection($this->args['articleId']);

        if ($collection) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($collection));
        } else {
            $this->response->setStatusCode(404);
            $this->response->setBody('No article with articleId ' . $this->args['articleId']);
        }
    }

}
