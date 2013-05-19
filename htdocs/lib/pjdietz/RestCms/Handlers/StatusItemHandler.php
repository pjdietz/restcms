<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\StatusController;

class StatusItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $controller = new StatusController();
        $status = $controller->readItem($this->args);

        if ($status) {
            $this->response->statusCode = 200;
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->body = json_encode($status);
        } else {
            $this->response->statusCode = 404;
            $this->response->body = 'No status with name ' . $this->args['$status'];
        }
    }
}
