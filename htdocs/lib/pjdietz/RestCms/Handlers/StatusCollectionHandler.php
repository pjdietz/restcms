<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\StatusController;

class StatusCollectionHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $controller = new StatusController();
        $collection = $controller->readCollection($this->request->query);

        $this->response->statusCode = 200;
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->body = json_encode($collection);
    }
}
