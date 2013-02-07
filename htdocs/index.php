<?php

require_once('vendor/autoload.php');
require_once('config/config.php');

$router = new \pjdietz\RestCms\MainRouter();
$response = $router->getResponse();
$response->respond();
exit;
