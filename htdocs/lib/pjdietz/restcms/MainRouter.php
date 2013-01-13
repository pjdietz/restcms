<?php

namespace pjdietz\restcms;

use \pjdietz\WellRESTed\Router;
use \pjdietz\WellRESTed\Route;

class MainRouter extends Router
{

    public function __construct()
    {
        parent::__construct();
        $this->addTemplate('/articles/', 'ArticleCollectionHandler');
        $this->addTemplate('/articles/{articleId}', 'ArticleItemHandler', array('articleId' => Route::RE_NUM));
        $this->addTemplate('/articles/{slug}', 'ArticleItemHandler', array('slug' => Route::RE_SLUG));
    }

    protected function addTemplate($template, $handler, $variables = null)
    {
        $this->addRoute(Route::newFromUriTemplate(
                $template,
                __NAMESPACE__ . '\\handlers\\' . $handler,
                null,
                $variables));
    }

}
