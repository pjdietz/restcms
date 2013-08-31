<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\ContributorModel;
use pjdietz\RestCms\Models\TagModel;
use pjdietz\RestCms\Models\UserModel;

class TagItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $tag = TagModel::init($this->args['tagId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($tag));
    }
}
