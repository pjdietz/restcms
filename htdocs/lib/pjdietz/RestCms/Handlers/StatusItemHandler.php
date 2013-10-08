<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\StatusModel;

class StatusItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $status = StatusModel::init($this->args['statusId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($status));
    }
}
