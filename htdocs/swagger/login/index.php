<?php

$username = '';
$message = '';

session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}
session_write_close();

if (isset($_GET['error']) && $_GET['error'] !== '') {
    $message = $_GET['error'];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Log In</title>
    <meta charset="utf-8" />
</head>
<body>
<h1>Log In</h1>
<?php print $message; ?>
<form action="confirm.php" method="POST">
    <label for="username">Username:</label>
    <input id="username" name="username" type="text" value="<?php echo $username;?>" required/>
    <label for="password">Password:</label>
    <input id="password" name="password" type="password" required/>
    <input type="submit" value="Log In" />
</form>
</body>
</html>
