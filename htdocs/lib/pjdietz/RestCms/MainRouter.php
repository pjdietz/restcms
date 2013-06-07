<?php

namespace pjdietz\RestCms;

use \pjdietz\WellRESTed\Router;
use \pjdietz\WellRESTed\Route;

class MainRouter extends Router
{
    const HANDLER_NAMESPACE = '\\pjdietz\\RestCms\\Handlers\\';

    private $templateVariables;

    public function __construct()
    {
        parent::__construct();

        $this->templateVariables = array(
            'articleId' => Route::RE_NUM,
            'versionId' => Route::RE_NUM
        );

        $this->addTemplate('/articles/', 'ArticleCollectionHandler');
        $this->addTemplate('/articles/{articleId}', 'ArticleItemHandler');
        $this->addTemplate('/articles/{articleId}/versions/', 'VersionCollectionHandler');
        $this->addTemplate('/articles/{articleId}/versions/{versionId}', 'VersionItemHandler');

        /*
        $this->addTemplate('/articles/{articleId}/content', 'ArticleContentItemHandler', array('articleId' => Route::RE_NUM));
        $this->addTemplate('/articles/{slug}/content', 'ArticleContentItemHandler', array('slug' => Route::RE_SLUG));
        $this->addTemplate('/articles/{slug}/versions/', 'VersionCollectionHandler', array('slug' => Route::RE_SLUG));
        $this->addTemplate('/articles/{slug}/versions/{articleVersionId}', 'VersionItemHandler', array('slug' => Route::RE_SLUG));
        $this->addTemplate('/status/', 'StatusCollectionHandler');
        $this->addTemplate('/status/{status}', 'StatusItemHandler', array('status' => Route::RE_SLUG));
        $this->addTemplate('/test/{id}', 'TestHandler', array('id' => Route::RE_NUM));
        */
    }

    private function addTemplate($template, $handler, $variables = null)
    {
        if (is_null($variables)) {
            $variables = $this->templateVariables;
        }

        $this->addRoute(Route::newFromUriTemplate(
                $template,
                self::HANDLER_NAMESPACE . $handler,
                $variables));
    }
}
