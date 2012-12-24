<?php

namespace restcms\handlers;

require_once(dirname(__FILE__) . '/RestCmsBaseHandler.inc.php');

class ArticleItemHandler extends RestCmsBaseHandler {

    protected function get() {

        $this->response->statusCode = 200;
        $this->response->body = 'A single article';

    }

    protected function put() {

        $this->response->statusCode = 200;
        $this->response->body = 'You updated this article.';

    }

    protected function delete() {

        $this->response->statusCode = 200;
        $this->response->body = 'You deleted this article.';

    }

}

?>
