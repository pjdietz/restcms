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
        $reader = $this->configuration["articleReader"];

        $article = $reader->read($id, $db);

        $this->response->setStatusCode(200);
        $this->response->setHeader("Content-type", "application/json");
        $this->response->setBody(json_encode($article));
    }
}
