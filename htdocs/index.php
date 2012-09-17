<?php

require_once('restcms/MainRouter.inc.php');

$router = new MainRouter();
$handler = $router->getRequestHandler();
$response = $handler->getResponse();
$response->respond();
exit;

?>
