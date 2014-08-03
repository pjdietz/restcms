<?php

namespace pjdietz\RestCms\Status;

use pjdietz\RestCms\Handler;

class StatusHandler extends Handler
{
    protected $db;

    protected function getAllowedMethods()
    {
        return array("GET", "HEAD", "OPTIONS");
    }

    protected function get()
    {
        $modelClass = $this->configuration->getClass("Status");
        $db = $this->configuration->getDatabaseConnection();

        /** @var \pjdietz\RestCms\Tag\Tag $modelClass */
        $status = $modelClass::init($this->args['statusId'], $db);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($status));
    }
}
