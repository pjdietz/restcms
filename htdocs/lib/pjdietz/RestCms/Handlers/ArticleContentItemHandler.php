<?php

namespace pjdietz\RestCms\Handlers;

use \pjdietz\RestCms\Controllers\ArticleItemController;

class ArticleContentItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        if (isset($this->args['articleId'])) {
            $article = ArticleItemController::newFromArticleId($this->args['articleId']);
            if ($article) {
                $articleData = $article->getData();
            } else {
                $this->response->statusCode = 404;
                $this->response->body = 'No article with articleId ' . $this->args['articleId'];
            }
        } elseif (isset($this->args['slug'])) {
            $article = ArticleItemController::newFromSlug($this->args['slug']);
            if ($article) {
                $articleData = $article->getData();
            } else {
                $this->response->statusCode = 404;
                $this->response->body = 'No article with slug ' . $this->args['slug'];
            }
        }

        if (isset($articleData)) {
            $this->response->setHeader('Content-Type', $articleData->contentType);
            $this->response->body = $articleData->content;
        } else {
            $this->response->statusCode = 400;
        }
    }

    protected function put()
    {
        // TODO Accept new content, create a new version with only content changed, and output the new article
        $this->response->statusCode = 200;
    }

}
