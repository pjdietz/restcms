<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\VersionModel;

class VersionItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $version = VersionModel::init($this->args['articleId'], $this->args['versionId']);

        if ($version) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($version));
        } else {
            $this->respondWithNotFoundError();
        }
    }
}
