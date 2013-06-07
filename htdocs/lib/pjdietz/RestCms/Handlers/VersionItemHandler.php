<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\VersionItemController;

class VersionItemHandler extends RestCmsBaseHandler {

    protected function get() {

        $controller = new VersionItemController();
        $version = $controller->read($this->args['articleId'], $this->args['versionId']);

        if ($version) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($version));
        } else {
            $this->response->setStatusCode(404);
            $this->response->setBody("No article version for articleId={$this->args['articleId']}, articleVersionId={$this->args['articleVersionId']}");
        }

    }

}
