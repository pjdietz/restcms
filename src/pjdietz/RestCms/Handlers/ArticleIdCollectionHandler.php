<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Lists\ArticleIdList;

class ArticleIdCollectionHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $collection = ArticleIdList::init($this->request->getQuery());

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($collection));
    }
}
