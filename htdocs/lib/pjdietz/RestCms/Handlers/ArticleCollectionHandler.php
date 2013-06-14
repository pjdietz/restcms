<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;

class ArticleCollectionHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'POST');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $collection = ArticleModel::initCollection($this->request->getQuery());

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($collection));
    }

    protected function post()
    {
        $this->user->assertPrivilege(self::PRIV_CREATE_ARTICLE);

        $article = ArticleModel::initWithJson($this->request->getBody(), $validator);

        // Attempt to add this to the database.
        $article->create();

        // Set the current user as a contributor for the new article.
        $article->addContributor($this->user);

        $this->response->setStatusCode(201);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($article));
    }

}
