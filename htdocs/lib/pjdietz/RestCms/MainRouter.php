<?php

namespace pjdietz\RestCms;

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
        $this->addTemplate('/articles/{articleId}/content', 'ArticleContentItemHandler', array('articleId' => Route::RE_NUM));
        $this->addTemplate('/articles/{slug}/content', 'ArticleContentItemHandler', array('slug' => Route::RE_SLUG));
        $this->addTemplate('/articles/{articleId}/versions/', 'ArticleVersionCollectionHandler', array('articleId' => Route::RE_NUM));
        $this->addTemplate('/articles/{slug}/versions/', 'ArticleVersionCollectionHandler', array('slug' => Route::RE_SLUG));

//        $this->addTemplate('/article/{articleId}/version/{articleVersionId}', 'ArticleContentItemHandler', array('articleId' => Route::RE_NUM));
//        $this->addTemplate('/article/{slug}/version/{articleVersionId}', 'ArticleContentItemHandler', array('slug' => Route::RE_SLUG));
    }

    protected function addTemplate($template, $handler, $variables = null)
    {
        $this->addRoute(Route::newFromUriTemplate(
                $template,
                'pjdietz\\RestCms\\Handlers\\' . $handler,
                $variables));
    }
}
