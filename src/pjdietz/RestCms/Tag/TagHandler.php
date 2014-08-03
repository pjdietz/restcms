<?php

namespace pjdietz\RestCms\Tag;

use pjdietz\RestCms\Handler;

class TagHandler extends Handler
{
    protected $db;

    protected function getAllowedMethods()
    {
        return array("GET", "HEAD", "OPTIONS");
    }

    protected function get()
    {
        $modelClass = $this->configuration->getClass("Tag");
        $db = $this->configuration->getDatabaseConnection();

        /** @var \pjdietz\RestCms\Tag\Tag $modelClass */
        $tag = $modelClass::init($this->args['tagId'], $db);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($tag));
    }
}
