<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Exceptions\ResourceException;
use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\VersionModel;

class CurrentVersionHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'PUT');
    }

    protected function get()
    {
        // Ensure the user may modify this article.
        $article = ArticleModel::initWithId($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'text/plain');
        $this->response->setBody($article->currentVersionId);
    }

    protected function put()
    {
        // Ensure the user may modify this article.
        $article = ArticleModel::initWithId($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        // Read the version to set as current.
        $versionId = trim($this->request->getBody());
        if (!is_numeric($versionId)) {
            $this->response->setStatusCode(400);
            $this->response->setHeader('Content-Type', 'text/plain');
            $this->response->setBody("Request body must be the integer ID of the version to promote to current. (text/plain)");
            return;
        }

        try {
            $version = VersionModel::init($this->args['articleId'], $versionId);
        } catch (ResourceException $e) {
            if ($e->getCode() === ResourceException::NOT_FOUND) {
                throw new ResourceException(
                    "Version {$versionId} does not belong to this article.",
                    ResourceException::INVALID_DATA
                );
            }
            throw $e;
        }

        // Fail if the passed version is already the current version.
        if ($version->isCurrent) {
            throw new ResourceException(
                "Version {$version->versionId} is already the current version.",
                ResourceException::CONFLICT
            );
        }

        // Point the article to the new version.
        $article->setCurrentVersion($version);

        // Out the article.
        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($article));
    }

}
