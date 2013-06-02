<?php

namespace pjdietz\RestCms\Handlers;

use \pjdietz\RestCms\Controllers\ArticleItemController;

class ArticleItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET', 'PUT', 'DELETE');
    }

    protected function get()
    {
        $controller = new ArticleItemController();
        $article = $controller->readFromOptions($this->args);

        if ($article) {
            $this->response->setStatusCode(200);
            $this->response->setHeader('Content-Type', 'application/json');
            $this->response->setBody(json_encode($article));
        } else {
            $this->response->setStatusCode(404);
            if (isset($this->args['articleId'])) {
                $this->response->setBody('No article with articleId ' . $this->args['articleId']);
            } elseif (isset($this->args['slug'])) {
                $this->response->setBody('No article with slug ' . $this->args['slug']);
            }
        }
    }

    protected function put()
    {
        // Attempt to build an article from the passed request body.
        $controller = new ArticleItemController();
        $article = $controller->readFromJson($this->request->getBody(), $validator);

        if ($article === null) {

            // Unable to validate.
            $this->response->setStatusCode(400);
            $this->response->setHeader('Content-type', 'application/json');

            $errors = array();

            /** @var \JsonSchema\Validator $validator */
            foreach ($validator->getErrors() as $error) {
                $errors[$error['property']] = $error['message'];
            }

            $output = array(
                'errors' => $errors
            );

            $this->response->setBody(json_encode($output));
            $this->response->respond();
            exit;
        }

        // TODO Write to database
        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-type', 'application/json');
        $this->response->setBody(json_encode($article->data));
    }

    protected function delete()
    {
        // TODO Write to database
        $this->response->setStatusCode(200);
        $this->response->setBody('You deleted this article.');
    }

}
