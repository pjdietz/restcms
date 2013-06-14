<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;

class ArticleItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'PUT', 'DELETE');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $article = ArticleModel::initWithId($this->args['articleId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($article));
    }

    protected function put()
    {
        $this->user->assertPrivilege(self::PRIV_CREATE_ARTICLE);

        // Attempt to build an article from the passed request body.
        $article = ArticleModel::initWithJson($this->request->getBody(), $validator);

        if (is_null($article)) {
            $schema = 'http://' . $_SERVER['HTTP_HOST'] . ArticleModel::PATH_TO_SCHEMA;
            $this->respondWithInvalidJsonError($validator, $schema);
            exit;
        }

        // TODO Write to database
        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->setBody(json_encode($article));
    }

    protected function delete()
    {
        // TODO Write to database
        $this->response->setStatusCode(200);
        $this->response->setBody('You deleted this article.');
    }

}
