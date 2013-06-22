<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;

class ArticleItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'PUT', 'DELETE');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $article = ArticleModel::init($this->args['articleId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($article));
    }

    protected function put()
    {
        // Ensure the user may modify this article.
        $article = ArticleModel::init($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        // Attempt to build an article from the passed request body.
        $newArticle = ArticleModel::initWithJson($this->request->getBody(), $validator);

        // Update the instance with data from the new article.
        $article->updateFrom($newArticle);

        // Write the current state to the database.
        $article->update();

        // Output the current representation.
        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->setBody(json_encode($article));
    }

    protected function delete()
    {
        // Ensure the user may modify this article.
        $article = ArticleModel::init($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        // Remove the article from the database.
        $article->delete();

        $this->response->setStatusCode(200);
        $this->response->setBody('You deleted this article.');
    }

}
