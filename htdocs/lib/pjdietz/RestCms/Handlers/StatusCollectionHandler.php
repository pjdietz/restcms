<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\StatusModel;

class StatusCollectionHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $collection = StatusModel::initCollection($this->request->getQuery());

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($collection));
    }
}
