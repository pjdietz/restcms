<?php

require_once('vendor/autoload.php');
require_once('lib/restcms/config.php');

$router = new \restcms\MainRouter();
$response = $router->getResponse();
$response->respond();
exit;

?>
