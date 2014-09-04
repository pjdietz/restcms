<?php

namespace pjdietz\RestCms\Article;

use pjdietz\RestCms\Handler;

class ArticleByPathHandler extends Handler
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

        /** @var \pjdietz\RestCms\Article\ArticleReader $reader */
        $reader = $this->configuration["articleReader"];
        $article = $reader->readWithPath($db, $path, $locale);

        $this->response->setStatusCode(200);
        $this->response->setHeader("Content-type", "application/json");
        $this->response->setBody(json_encode($article));
    }
}
