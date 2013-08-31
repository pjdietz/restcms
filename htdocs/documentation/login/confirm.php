<?php

use pjdietz\RestCms\Models\UserModel;
use RestCmsConfig\ConfigInterface;

require_once('../../vendor/autoload.php');

// Ensure the script recieved username and password variables.
if (isset($_POST['username']) && $_POST['username'] !== '') {
    $username = $_POST['username'];
} else {
    exit;
}
if (isset($_POST['password']) && $_POST['password'] !== '') {
    $password = $_POST['password'];
} else {
    exit;
}

// Create a hash for the password.
$passwordHash = hash('sha256', $password . ConfigInterface::SALT);

// Create a user instance with this username
$user = UserModel::initWithUsername($username);

// Check if the supplied password matches.
if ($user->passwordHash === $passwordHash) {
    // Password is correct. Store to session and redirect.
    session_start();
    $_SESSION['username'] = $username;
    $_SESSION['passwordHash'] = $passwordHash;
    session_write_close();
    header('Location: /documentation/');
    exit;
} else {
    // Password is incorrect. Store the username to the session, and redirect with error.
    session_start();
    $_SESSION['username'] = $username;
    session_write_close();
    header('Location: /documentation/login/?error=The+password+you+entered+is+incorrect.');
    exit;
}
