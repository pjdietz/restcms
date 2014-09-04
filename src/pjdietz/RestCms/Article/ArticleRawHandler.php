<?php

namespace pjdietz\RestCms\Article;

use pjdietz\RestCms\Handler;

class ArticleRawHandler extends Handler
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
        $article = $reader->read($db, $id);

        $this->response->setStatusCode(200);
        $this->response->setHeader("Content-type", $article->contentType);
        $this->response->setBody($article->content);
    }
}
