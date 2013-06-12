<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\StatusModel;

class StatusItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $status = null;
        if (isset($this->args['statusId'])) {
            $status = StatusModel::initWithId($this->args['statusId']);
        } elseif (isset($this->args['statusSlug'])) {
            $status = StatusModel::initWithSlug($this->args['statusSlug']);
        }

        if ($status) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($status));
        } else {
            $this->respondWithNotFoundError();
        }
    }
}
