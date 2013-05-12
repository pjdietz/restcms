<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\ArticleVersionItemController;

class ArticleVersionItemHandler extends RestCmsBaseHandler {

    protected function get() {

        if (!isset($this->args['articleVersionId'])) {
            $this->response->statusCode = 400;
            return;
        }

        if (isset($this->args['articleId'])) {
            $item = ArticleVersionItemController::newFromArticleId($this->args['articleId'], $this->args['articleVersionId']);
            if ($item) {
                $itemData = $item->getData();
            } else {
                $this->response->statusCode = 404;
                $this->response->body = 'No article version for articleId=' . $this->args['articleId'] . ', articleVersionId=' . $this->args['articleVersionId'];
            }
        } elseif (isset($this->args['slug'])) {
            $item = ArticleVersionItemController::newFromSlug($this->args['slug'], $this->args['articleVersionId']);
            if ($item) {
                $itemData = $item->getData();
            } else {
                $this->response->statusCode = 404;
                $this->response->body = 'No article version for slug=' . $this->args['slug'] . ', articleVersionId=' . $this->args['articleVersionId'];
            }
        }

        if (isset($itemData)) {
            $this->response->statusCode = 200;
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->body = json_encode($itemData);
        } else {
            $this->response->statusCode = 400;
        }

    }

}
