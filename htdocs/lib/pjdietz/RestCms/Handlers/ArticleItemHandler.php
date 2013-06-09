<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\ArticleController;
use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\UserModel;

class ArticleItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'PUT', 'DELETE');
    }

    protected function get()
    {
        $this->assertUserPrivileges(self::PRIV_READ_ARTICLE);

//        $controller = new ArticleController();
//        $article = $controller->readItem($this->args['articleId']);

        $article = ArticleModel::newById($this->args['articleId']);

        if ($article) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($article));
        } else {
            $this->response->setStatusCode(404);
            $this->response->setBody('No article with articleId ' . $this->args['articleId']);
        }
    }

    protected function put()
    {
        $this->assertUserPrivileges(self::PRIV_CREATE_ARTICLE);

        // Attempt to build an article from the passed request body.
        $controller = new ArticleController();
        $article = $controller->parseJson($this->request->getBody(), $validator);

        if (is_null($article)) {
            $schema = 'http://' . $_SERVER['HTTP_HOST'] . ArticleController::PATH_TO_SCHEMA;
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
