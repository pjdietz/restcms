<?php

require_once('restcms/MainRouter.inc.php');

$router = new MainRouter();
$response = $router->getResponse();
$response->respond();
exit;

?>
