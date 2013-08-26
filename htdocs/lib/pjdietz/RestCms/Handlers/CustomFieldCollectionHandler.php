<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\CustomFieldModel;

class CustomFieldCollectionHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'POST');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);
        $article = ArticleModel::init($this->args['articleId']);
        $collection = CustomFieldModel::initCollection($article->articleId);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setHeader('X-count', count($collection));
        $this->response->setBody(json_encode($collection));
    }

    protected function post()
    {
        $article = ArticleModel::init($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        $customField = CustomFieldModel::initWithJson($this->request->getBody());
        $customField->create($article->articleId);

        $this->response->setStatusCode(201);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($customField));
    }
}

