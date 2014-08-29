<?php

namespace pjdietz\RestCms;

use pjdietz\WellRESTed\Interfaces\RequestInterface;
use pjdietz\WellRESTed\Routes\TemplateRoute;

/**
 * Provide the route table.
 */
class Router extends \pjdietz\WellRESTed\Router
{
    private $config;

    public function __construct($config)
    {
        parent::__construct();
        $this->config = $config;
        $this->addRoutes(
            array(
                new TemplateRoute("/articles/{articleId}", $config["ArticleHandler"])
            )
        );
    }

    public function getResponse(RequestInterface $request, array $args = null)
    {
        $arguments = array("configuration" => $this->config);
        if ($args) {
            $arguments = array_merge($arguments, $args);
        }
        return parent::getResponse($request, $arguments);
    }
}