<?php

namespace restcms\handlers;

require_once('wellrested/Handler.inc.php');

abstract class ArticleCollectionHandler extends \wellrested\Handler {

    protected function get() {

        $this->response->statusCode = 200;
        $this->response->body = 'List of articles';

    }

    protected function post() {

        $this->response->statusCode = 201;
        $this->response->body = 'You added an article.';

    }

}

?>
