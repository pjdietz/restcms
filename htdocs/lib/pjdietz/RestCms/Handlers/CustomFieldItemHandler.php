<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\ArticleModel;
use pjdietz\RestCms\Models\CustomFieldModel;

class CustomFieldItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'PUT');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);
        $article = ArticleModel::init($this->args['articleId']);
        $item = CustomFieldModel::init($article->articleId, $this->args['customFieldId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($item));
    }

    protected function put()
    {
        // Read the article and assert the user may modify it.
        $article = ArticleModel::init($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        // Read the request body as a custom field.
        // Ensure the IDs in the URI are assigned in the object.
        // Update the record.
        $customField = CustomFieldModel::initWithJson($this->request->getBody());
        $customField->articleId = $article->articleId;
        $customField->customFieldId = $this->args['customFieldId'];
        $customField->update($article);

        // Re-read the custom field from the database.
        $customField = CustomFieldModel::init($article->articleId, $this->args['customFieldId']);
        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($customField));
    }

    protected function delete()
    {
        // Read the article and assert the user may modify it.
        $article = ArticleModel::init($this->args['articleId']);
        $this->user->assertArticleAccess($article);

        // Read and delete the custom field.
        $customField = CustomFieldModel::init($article->articleId, $this->args['customFieldId']);
        $customField->delete();

        // Respond with no content.
        $this->response->setStatusCode(204);
    }
}

