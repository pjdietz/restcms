<?php

use pjdietz\RestCms\Configuration;
use pjdietz\RestCms\Router;
use pjdietz\WellRESTed\Request;

ini_set("display_errors", 0);

require_once(__DIR__ . "/../vendor/autoload.php");

$router = new Router();
$configuration = new Configuration(); // TODO Replace with custom config for testing
$response = $router->getResponse(Request::getRequest(), array("configuration" => $configuration));
$response->respond();
exit;

