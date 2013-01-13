<?php

require_once('vendor/autoload.php');

$resp = new \pjdietz\WellRESTed\Response();
$resp->statusCode = 200;
$resp->body = 'AUTO LOADED!';
$resp->respond();
exit;

?>
