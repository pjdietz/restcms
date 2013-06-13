<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ContributorModel;

class ContributorCollectionHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $collection = ContributorModel::initCollection($this->args['articleId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($collection));
    }
}