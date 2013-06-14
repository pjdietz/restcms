<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\ContributorModel;

class ContributorItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $contributor = ContributorModel::initWithId($this->args['articleId'], $this->args['userId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($contributor));
    }
}
