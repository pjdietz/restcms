<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\ArticleVersionCollectionController;

class ArticleVersionCollectionHandler extends RestCmsBaseHandler {

    protected function get() {

        if (isset($this->args['articleId'])) {
            $collection = ArticleVersionCollectionController::newFromArticleId($this->args['articleId']);
            if ($collection) {
                $collectionData = $collection->getData();
            } else {
                $this->response->statusCode = 404;
                $this->response->body = 'No article with articleId ' . $this->args['articleId'];
            }
        } elseif (isset($this->args['slug'])) {
            $collection = ArticleVersionCollectionController::newFromSlug($this->args['slug']);
            if ($collection) {
                $collectionData = $collection->getData();
            } else {
                $this->response->statusCode = 404;
                $this->response->body = 'No article with slug ' . $this->args['slug'];
            }
        }

        if (isset($collectionData)) {
            $this->response->statusCode = 200;
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->body = json_encode($collectionData);
        } else {
            $this->response->statusCode = 400;
        }

    }

}
