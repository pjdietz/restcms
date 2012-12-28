<?php

namespace restcms\config;

// Provide settings for MySQL.
const MYSQL_HOSTNAME = 'localhost';
const MYSQL_USERNAME = 'localuser';
const MYSQL_PASSWORD = 'maxPower';
const MYSQL_DATABASE = 'restcms';

// Authentication sheme:
//
// true: [Recommended] Require requests to be signed by hashing the request URI,
// method, and body with the username and passwordHash. This is more
// complicated, but prevents requests from being altered and replayed.
//
// false: Require only the username and passwordHash to be as credentials for
// requests. This is simpler, but less secure as a request could be sniffed,
// altered, and replayed.
//
const AUTH_USE_REQUEST_HASH = true;

?>
