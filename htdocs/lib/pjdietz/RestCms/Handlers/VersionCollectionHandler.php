<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Controllers\VersionCollectionController;

class VersionCollectionHandler extends RestCmsBaseHandler {

    protected function getAllowedMethods()
    {
        return array('GET', 'POST');
    }

    protected function get() {

        $controller = new VersionCollectionController();
        $collection = $controller->read($this->args['articleId']);

        if ($collection) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($collection));
        } else {
            $this->response->setStatusCode(404);
            $this->response->setBody('No article with articleId ' . $this->args['articleId']);
        }

    }

}
