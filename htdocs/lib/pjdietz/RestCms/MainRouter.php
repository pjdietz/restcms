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
            'statusId' => Route::RE_NUM,
            'statusSlug' => Route::RE_SLUG,
            'versionId' => Route::RE_NUM,
            'userId' => Route::RE_NUM
        );

        $this->addTemplate('/articles/', 'ArticleCollectionHandler');
        $this->addTemplate('/articles/{articleId}', 'ArticleItemHandler');
        $this->addTemplate('/articles/{articleId}/content', 'ArticleContentHandler');
        $this->addTemplate('/articles/{articleId}/contributors/', 'ContributorCollectionHandler');
        $this->addTemplate('/articles/{articleId}/contributors/{userId}', 'ContributorItemHandler');
        $this->addTemplate('/articles/{articleId}/current-version', 'CurrentVersionHandler');
        $this->addTemplate('/articles/{articleId}/versions/', 'VersionCollectionHandler');
        $this->addTemplate('/articles/{articleId}/versions/{versionId}', 'VersionItemHandler');
        $this->addTemplate('/status/', 'StatusCollectionHandler');
        $this->addTemplate('/status/{statusId}', 'StatusItemHandler');
        $this->addTemplate('/status/{statusSlug}', 'StatusItemHandler');
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
