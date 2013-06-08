<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\StatusController;

class StatusItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $controller = new StatusController();
        $status = $controller->readItem($this->args['statusId']);

        if ($status) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($status));
        } else {
            $this->response->setStatusCode(404);
            $this->response->setBody('No status with id ' . $this->args['statusId']);
        }
    }
}
