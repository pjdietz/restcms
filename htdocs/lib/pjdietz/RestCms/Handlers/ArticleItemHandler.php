<?php

namespace pjdietz\RestCms\Handlers;

use \pjdietz\RestCms\Controllers\ArticleItemController;

class ArticleItemHandler extends RestCmsBaseHandler
{
    protected function get()
    {
        $controller = new ArticleItemController();
        $article = $controller->readFromOptions($this->args);

        if ($article) {
            $this->response->statusCode = 200;
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->body = json_encode($article);
        } else {
            $this->response->statusCode = 404;
            if (isset($this->args['articleId'])) {
                $this->response->body = 'No article with articleId ' . $this->args['articleId'];
            } elseif (isset($this->args['slug'])) {
                $this->response->body = 'No article with slug ' . $this->args['slug'];
            }
        }
    }

    protected function put()
    {
        // Attempt to build an article from the passed request body.
        $article = ArticleItemController::newFromJson($this->request->body, $validator);

        if ($article === null) {

            // Unable to validate.
            $this->response->statusCode = 400;
            $this->response->setHeader('Content-type', 'application/json');

            $errors = array();

            foreach ($validator->getErrors() as $error) {
                $errors[$error['property']] = $error['message'];
            }

            $output = array(
                'errors' => $errors
            );

            $this->response->body = json_encode($output);

            $this->response->respond();
            exit;

        }

        // TODO Write to database

        $this->response->statusCode = 200;
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->body = json_encode($article->data);

    }

    protected function delete()
    {
        // TODO Write to database

        $this->response->statusCode = 200;
        $this->response->body = 'You deleted this article.';
    }

}
