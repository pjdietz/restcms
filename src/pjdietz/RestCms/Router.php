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
        $this->addRoutes(
            array(
                new TemplateRoute("/articles/{articleId}", __NAMESPACE__ . "\\Article\\ArticleHandler"),
            )
        );
    }
}
