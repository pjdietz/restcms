<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\ContributorModel;
use pjdietz\RestCms\Models\UserModel;

class ContributorCollectionHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'POST');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $collection = ContributorModel::initCollection($this->args['articleId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($collection));
    }

    protected function post()
    {
        // Ensure the user may modify this article.
        $article = ArticleModel::init($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        $article = ArticleModel::init($this->args['articleId']);

        $userId = trim($this->request->getBody());
        if (is_numeric($userId)) {
            $user = UserModel::initWithId((int) $userId);
        } else {
            $user = UserModel::initWithUsername($userId);
        }

        $article->addContributor($user);

        $collection = ContributorModel::initCollection($this->args['articleId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($collection));
    }
}
