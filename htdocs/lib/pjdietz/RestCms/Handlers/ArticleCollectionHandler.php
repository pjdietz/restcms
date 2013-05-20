<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\ArticleCollectionController;
use pjdietz\RestCms\Controllers\ArticleItemController;
use pjdietz\RestCms\Exceptions\DatabaseException;

class ArticleCollectionHandler extends RestCmsBaseHandler
{
    protected function get()
    {

        $this->readUser(false);

        // TODO Once I have users linked to articles and articles marked as public, etc. restrict this list if the user is not an admin.

        $controller = new ArticleCollectionController();
        $controller->readFromOptions($this->request->query);

        $this->response->statusCode = 200;
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->body = json_encode($controller->data);

    }

    protected function post()
    {
        $this->readUser(false);
        // TODO Once I have users linked to articles and articles marked as public, etc. restrict this list if the user is not an admin.

        $controller = new ArticleItemController();
        $article = $controller->readFromJson($this->request->body, $validator);

        // Fail if the JSON is borked.
        if (is_null($article)) {
            $schema = 'http://' .  $_SERVER['HTTP_HOST'] . ArticleItemController::PATH_TO_SCHEMA;
            $this->respondWithInvalidJsonError($validator, $schema);
            exit;
        }

        // Attempt to add this to the database.
        try {
            $article = $controller->insert();
        } catch (DatabaseException $e) {
            $this->response->statusCode = $e->getCode();
            $this->response->body = $e->getMessage();
            return;
        }

        $this->response->statusCode = 201;
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->body = json_encode($article);

    }

}
