<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\TagModel;

class TagCollectionHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $collection = TagModel::initCollection($this->request->getQuery());

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setHeader('X-Count', count($collection));
        $this->response->setBody(json_encode($collection));
    }
}
