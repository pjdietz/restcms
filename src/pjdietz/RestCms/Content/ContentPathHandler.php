<?php

namespace pjdietz\RestCms\Content;

use pjdietz\RestCms\Handler;

class ContentPathHandler extends Handler
{
    protected function getAllowedMethods()
    {
        return array("GET", "HEAD", "OPTIONS");
    }

    protected function get()
    {
        $db = $this->configuration["db"];
        $path = $this->args["path"];

        $locale = null;
        $query = $this->request->getQuery();
        if (isset($query["locale"])) {
            $locale = $query["locale"];
        }

        /** @var \pjdietz\RestCms\Content\ContentReader $reader */
        $reader = $this->configuration["contentReader"];
        $content = $reader->readWithPath($db, $path, $locale);

        if (isset($query["content"])) {
            $this->response->setStatusCode(200);
            $this->response->setHeader("Content-type", $content->contentType);
            $this->response->setBody($content->content);
        } else {
            $this->response->setStatusCode(200);
            $this->response->setHeader("Content-type", "application/json");
            $this->response->setBody(json_encode($content));
        }

    }
}
