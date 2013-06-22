<?php

require_once('vendor/autoload.php');

$router = new \pjdietz\RestCms\MainRouter();
$response = $router->getResponse();
$response->respond();
exit;
