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
