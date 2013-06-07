<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\ArticleController;

class ArticleItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'PUT', 'DELETE');
    }

    protected function get()
    {
        $controller = new ArticleController();
        $article = $controller->readItem($this->args['articleId']);

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
