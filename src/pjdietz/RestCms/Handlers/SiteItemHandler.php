<?php

namespace pjdietz\RestCms\Handlers;

use pjdietz\RestCms\Models\SiteModel;

class SiteItemHandler extends RestCmsBaseHandler
{
    protected function getAllowedMethods()
    {
        return array('GET');
    }

    protected function get()
    {
        $this->user->assertPrivilege(self::PRIV_READ_ARTICLE);

        $site = SiteModel::init($this->args['siteId']);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($site));
    }
}
