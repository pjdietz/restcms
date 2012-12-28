<?php

namespace restcms\handlers;

require_once(dirname(__FILE__) . '/RestCmsBaseHandler.inc.php');

class ArticleCollectionHandler extends RestCmsBaseHandler {

    protected function get() {

        $this->response->statusCode = 200;
        $this->response->body = 'List of articles';

    }

    protected function post() {

        $this->readUser(true);

        $this->response->statusCode = 201;
        $this->response->body = 'You added an article.';

    }

}

?>
