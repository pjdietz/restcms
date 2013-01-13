<?php

require_once('vendor/autoload.php');
require_once('lib/pjdietz/restcms/config.php');

$router = new \pjdietz\restcms\MainRouter();
$response = $router->getResponse();
$response->respond();
exit;
