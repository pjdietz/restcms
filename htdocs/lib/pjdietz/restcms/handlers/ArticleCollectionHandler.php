<?php

namespace pjdietz\restcms\handlers;

use pjdietz\restcms\controllers\ArticleCollectionController;

class ArticleCollectionHandler extends RestCmsBaseHandler {

    protected function get() {

        $this->readUser(false);

        // TODO Once I have users linked to articles and articles marked as public, etc. restrict this list if the user is not an admin.

        $articles = new ArticleCollectionController();

        $this->response->statusCode = 200;
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->body = json_encode($articles->data);

    }

    protected function post() {

        $this->readUser(true);

        $this->response->statusCode = 201;
        $this->response->body = 'You added an article.';

    }

}
