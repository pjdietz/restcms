<?php

require_once('wellrested/Router.inc.php');

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
$response = $handler->getResponse();
$response->respond();
exit;

?>
