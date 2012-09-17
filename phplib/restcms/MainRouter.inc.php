<?php

require_once('wellrested/Router.inc.php');

class MainRouter extends \wellrested\Router {

    public function __construct() {

        parent::__construct();

        $this->addRoute(new \wellrested\Route('/^\/cat\/$/', 'CatHandler', 'restcms/handlers/CatHandler.inc.php'));

        $this->addRoute(\wellrested\Route::newFromUriTemplate(
            '/cat/{catId}',
            'CatHandler',
            'restcms/handlers/CatHandler.inc.php',
            array('catId' => \wellrested\Route::RE_NUM)));

        $this->addTemplate('/mouse/', 'MouseHandler');
        $this->addTemplate('/mouse/{mouseId}', 'MouseHandler', array('mouseId' => \wellrested\Route::RE_NUM));

    }

    protected function addTemplate($template, $handler, $variables=null) {
        $this->addRoute(\wellrested\Route::newFromUriTemplate(
            $template,
            $handler,
            'restcms/handlers/' . $handler . '.inc.php',
            $variables
        ));
    }

}

?>