<?php

/*
 * Replace fields in the file matched by the request URI and output.
 */

$filename = basename($_SERVER['REQUEST_URI']);
$filepath = dirname(__FILE__) . '/' . $filename;

if (file_exists($filepath)) {
    $file = file_get_contents($filepath);
    $fields = array(
        '{{HOSTNAME}}' => $_SERVER['HTTP_HOST']
    );
    $file = str_replace(
        array_keys($fields),
        array_values($fields),
        $file);
    header('Content-type: application/json');
    print $file;
} else {
    header('HTTP/1.1 404 NOT FOUND');
}
