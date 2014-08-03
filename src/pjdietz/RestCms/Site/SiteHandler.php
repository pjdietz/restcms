<?php

namespace pjdietz\RestCms\Site;

use pjdietz\RestCms\Handler;

class SiteHandler extends Handler
{
    protected $db;

    protected function getAllowedMethods()
    {
        return array("GET", "HEAD", "OPTIONS");
    }

    protected function get()
    {
        $siteModelClass = $this->configuration->getClass("Site");
        $db = $this->configuration->getDatabaseConnection();

        /** @var \pjdietz\RestCms\Site\Site $siteModelClass */
        $site = $siteModelClass::init($this->args['siteId'], $db);

        $this->response->setStatusCode(200);
        $this->response->setHeader('Content-Type', 'application/json');
        $this->response->setBody(json_encode($site));
    }
}
