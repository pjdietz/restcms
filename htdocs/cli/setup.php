<?php

require_once(dirname(__FILE__) . '/../config/config.php');
require_once(dirname(__FILE__) . '/../vendor/autoload.php');

use pjdietz\restcms\CLI\SetupApp;
use pjdietz\CliApp\CliAppException;

// -----------------------------------------------------------------------------

$app = new SetupApp();

try {
    $status = $app->run();
} catch (CliAppException $e) {
    exit($e->getMessage() . "\n");
}

exit($status);
