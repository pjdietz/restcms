<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\VersionModel;

class VersionItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        // Ensure the user may modify this article.
        $article = ArticleModel::initWithId($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        $version = VersionModel::init($this->args['articleId'], $this->args['versionId']);

        if ($version) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($version));
        } else {
            $this->respondWithNotFoundError();
        }
    }
}
