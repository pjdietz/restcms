<?php

namespace restcms;

require_once('wellrested/Router.inc.php');

class MainRouter extends \wellrested\Router {

    public function __construct() {

        parent::__construct();

        $this->addTemplate('/articles/', 'ArticleCollectionHandler');
        $this->addTemplate('/articles/{articleId}', 'ArticleItemHandler',  array('articleId' => \wellrested\Route::RE_NUM));
        $this->addTemplate('/articles/{slug}',  'ArticleItemHandler',  array('slug' => \wellrested\Route::RE_SLUG));

    }

    protected function addTemplate($template, $handler, $variables=null) {
        $this->addRoute(\wellrested\Route::newFromUriTemplate(
            $template,
            '\\restcms\\handlers\\' . $handler,
            'restcms/handlers/' . $handler . '.inc.php',
            $variables
        ));
    }

}

?>