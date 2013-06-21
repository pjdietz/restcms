<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\ContributorModel;
use pjdietz\RestCms\Models\UserModel;

class ContributorItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'DELETE');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $contributor = ContributorModel::initWithId($this->args['articleId'], $this->args['userId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($contributor));
    }
    protected function delete()
    {
        // TODO assert user can modify this article.
        $article = ArticleModel::initWithId($this->args['articleId']);
        $this->user->assertArticleAccess($article);
        $user = UserModel::initWithId($this->args['userId']);

        if (!$article->hasContributor($user)) {
            $this->respondWithNotFoundError("User \"{$user->username}\" (userId {$user->userId}) is not a contributor for this article.");
        }

        $article->removeContributor($user);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'text/plain');
        $this->response->setBody("User \"{$user->username}\" (userId {$user->userId}) removed as a contributor for this article.");
    }
}
