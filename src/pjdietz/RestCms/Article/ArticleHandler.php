<?php

namespace pjdietz\RestCms\Article;

use pjdietz\RestCms\Handler;

class ArticleHandler extends Handler
{
    protected function getAllowedMethods()
    {
        return array("GET", "HEAD", "OPTIONS");
    }

    protected function get()
    {
        $id = $this->args["articleId"];
        $db = $this->configuration["db"];
        /** @var ArticleReader $reader */
        $reader = $this->configuration["articleReader"];
        $content = $reader->read($db, $id);

        $this->response->setStatusCode(200);
        $this->response->setHeader("Content-type", "application/json");
        $this->response->setBody(json_encode($content));
    }
}
