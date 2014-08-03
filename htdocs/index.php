<?php

use pjdietz\RestCms\Configuration;
use pjdietz\RestCms\Router;
use pjdietz\WellRESTed\Request;

if (!@include_once(__DIR__ . "/../vendor/autoload.php")) {
    header('Content-type: text/html');
    print <<<HTML
<!DOCTYPE html>
<html>
    <head>
        <title>Composer Install Required</title>
        <meta charset="utf-8" />
    </head>
    <body>
        <h1>Error: Composer Install Required</h1>
        <p>Unable locate autoload file. Please run <code>php composer.phar install</code> from the document root to continue.</p>
        <p>For information on downloading and using Composer, see <a href="http://getcomposer.org/">getcomposer.org</a> or <a href="https://packagist.org/">packagist.org</a></p>
    </body>
</html>
HTML;
    exit;
}

$router = new Router();
$configuration = new Configuration();
$response = $router->getResponse(Request::getRequest(), array("configuration" => $configuration));
$response->respond();
exit;
