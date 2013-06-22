<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;

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

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', $article->contentType);
        $this->response->setBody($article->content);
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

        // Output the current representation.
        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', $article->contentType);
        $this->response->setBody($article->content);
    }

}
