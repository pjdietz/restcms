<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Exceptions\DatabaseException;
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

        // Fail if the JSON is borked.
        if (is_null($article)) {
            $schema = 'http://' . $_SERVER['HTTP_HOST'] . ArticleModel::PATH_TO_SCHEMA;
            $this->respondWithInvalidJsonError($validator, $schema);
            exit;
        }

        // Attempt to add this to the database.
        try {
            $article->create();
        } catch (DatabaseException $e) {
            $this->response->setStatusCode($e->getCode());
            $this->response->setBody($e->getMessage());
            return;
        }

        $this->response->setStatusCode(201);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($article));
    }

}
