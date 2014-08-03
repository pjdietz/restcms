<?php

namespace pjdietz\RestCms;

use pjdietz\WellRESTed\Routes\TemplateRoute;

/**
 * Provide the route table.
 */
class Router extends \pjdietz\WellRESTed\Router
{
    public function __construct()
    {
        parent::__construct();
        $this->addRoutes(array(
               new TemplateRoute("/sites/{siteId}", __NAMESPACE__ . "\\Site\\SiteHandler"),
               new TemplateRoute("/tags/{tagId}",   __NAMESPACE__ . "\\Tag\\TagHandler")
            ));
    }
}
