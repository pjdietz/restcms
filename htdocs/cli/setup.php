<?php

use pjdietz\RestCms\CLI\SetupApp;
use pjdietz\CliApp\CliAppException;

// Fail if the script is not started from the command line.
if (php_sapi_name() !== 'cli') {
    exit;
}

require_once(dirname(__FILE__) . '/../vendor/autoload.php');

$app = new SetupApp();

try {
    $status = $app->run();
} catch (CliAppException $e) {
    exit($e->getMessage() . "\n");
}

exit($status);
