<?php

namespace restcms\handlers;

require_once(dirname(__FILE__) . '/RestCmsBaseHandler.inc.php');
require_once('restcms/controllers/ArticleItemController.inc.php');

class ArticleItemHandler extends RestCmsBaseHandler {

    protected function get() {

        if (isset($this->args['articleId'])) {
            $article = \restcms\controllers\ArticleItemController::newFromArticleId($this->args['articleId']);

            if ($article) {
                $this->response->statusCode = 200;
                $this->response->setHeader('Content-Type', 'application/json');
                $this->response->body = json_encode($article->data);
            } else {
                $this->response->statusCode = 404;
                $this->response->body = 'No article with articleId ' . $this->args['articleId'];
            }

        } elseif (isset($this->args['slug'])) {
            $article = \restcms\controllers\ArticleItemController::newFromSlug($this->args['slug']);

            if ($article) {
                $this->response->statusCode = 200;
                $this->response->setHeader('Content-Type', 'application/json');
                $this->response->body = json_encode($article->data);
            } else {
                $this->response->statusCode = 404;
                $this->response->body = 'No article with slug ' . $this->args['slug'];
            }

        } else {
            $this->response->statusCode = 400;
        }

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
