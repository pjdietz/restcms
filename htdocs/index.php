<?php

require_once('wellrested/Router.inc.php');

/*
\wellrested\Route::newFromTemplate('/cat/{catId}/mouse/{mouseId}/', array(
    'catId' => \wellrested\Route::RE_NUM
));

exit;
*/


class MyRouter extends \wellrested\Router {

    public function __construct() {
        parent::__construct();

        $this->handlerPathPattern = 'restcms/handlers/%s.inc.php';

        $this->addUriTemplate('/cat/',                        'CatHandler');
        $this->addUriTemplate('/cat/{catId}',                 'CatHandler');
        $this->addUriTemplate('/cat/{catId}/mouse/',          'MouseHandler');
        $this->addUriTemplate('/cat/{catId}/mouse/{mouseId}', 'MouseHandler');

    }

}


$router = new MyRouter();
$handler = $router->getRequestHandler();
$handler->respond();

?>
