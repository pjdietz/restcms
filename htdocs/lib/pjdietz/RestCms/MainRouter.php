<?php

namespace pjdietz\RestCms;

use \pjdietz\WellRESTed\Router;
use \pjdietz\WellRESTed\Route;

class MainRouter extends Router
{
    const HANDLER_NAMESPACE = '\\pjdietz\\RestCms\\Handlers\\';

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
        $this->addTemplate('/articles/{articleId}/versions/{articleVersionId}', 'ArticleVersionItemHandler', array('articleId' => Route::RE_NUM));
        $this->addTemplate('/articles/{slug}/versions/{articleVersionId}', 'ArticleVersionItemHandler', array('slug' => Route::RE_SLUG));
        $this->addTemplate('/status/', 'StatusCollectionHandler');
        $this->addTemplate('/status/{status}', 'StatusItemHandler', array('status' => Route::RE_SLUG));
        $this->addTemplate('/test/{id}', 'TestHandler', array('id' => Route::RE_NUM));
    }

    protected function addTemplate($template, $handler, $variables = null)
    {
        $this->addRoute(Route::newFromUriTemplate(
                $template,
                self::HANDLER_NAMESPACE . $handler,
                $variables));
    }
}
