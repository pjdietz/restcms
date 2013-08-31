<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Util\Util;

class ArticleContentHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'PUT');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $article = ArticleModel::init($this->args['articleId']);

        $query = $this->request->getQuery();
        if (isset($query['process']) and Util::stringToBool($query['process'])) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', $article->contentType);
            $this->response->setBody($article->content);
        } else {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'text/plain');
            $this->response->setBody($article->originalContent);
        }
    }

    protected function put()
    {
        // Ensure the user may modify this article.
        $article = ArticleModel::init($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        // Update the original content of the article with the request body.
        $article->setContent($this->request->getBody());

        // Write the current state to the database.
        $article->update();

        $query = $this->request->getQuery();
        if (isset($query['process']) and Util::stringToBool($query['process'])) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', $article->contentType);
            $this->response->setBody($article->content);
        } else {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'text/plain');
            $this->response->setBody($article->originalContent);
        }
    }
}
