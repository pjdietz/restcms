<?php

require_once('restcms/MainRouter.inc.php');

$router = new MainRouter();
$response = $router->getResponse();
$response->respond();

/*
$handler = $router->getRequestHandler();
$response = $handler->getResponse();
$response->respond();
*/

exit;

// TODO: Default response

?>
