<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\VersionController;

class VersionItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $controller = new VersionController();
        $item = $controller->readItem($this->args['articleId'], $this->args['versionId']);

        if ($item) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($item));
        } else {
            $this->response->setStatusCode(404);
            $this->response->setBody(
                sprintf(
                    "No article version for articleId=%d, versionId=%d",
                    $this->args['articleId'],
                    $this->args['articleVersionId']
                )
            );
        }
    }
}
