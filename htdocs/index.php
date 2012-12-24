<?php

require_once('restcms/MainRouter.inc.php');

$router = new \restcms\MainRouter();
$response = $router->getResponse();
$response->respond();
exit;

?>
