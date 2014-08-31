<?php

namespace pjdietz\RestCms\Content;

use pjdietz\RestCms\Handler;

class ContentHandler extends Handler
{
    protected function getAllowedMethods()
    {
        return array("GET", "HEAD", "OPTIONS");
    }

    protected function get()
    {
        $id = $this->args["contentId"];
        $db = $this->configuration["db"];
        /** @var ContentReader $reader */
        $reader = $this->configuration["contentReader"];
        $content = $reader->read($db, $id);

        $this->response->setStatusCode(200);
        $this->response->setHeader("Content-type", "application/json");
        $this->response->setBody(json_encode($content));
    }
}
