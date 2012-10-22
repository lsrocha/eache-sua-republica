<?php
session_start();

require 'includes/autoloader.php';

use core\Authentication;

if (Authentication::isLoggedIn()) {
    if (isset($_GET['logout']) && $_GET['logout']) {
        Authentication::logout();
    } else {
        header('Location: secret.php');
    }
} elseif (isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        if (!isset($_GET['goback'])) {
            $go = 'secret.php';
        } else {
            $go = $_GET['goback'];
        }

        Authentication::login($email, $password, $go);
    }		
}
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Republicas</title>
	</head>
	<body>
		<a href="registry.php">Sign up</a><br /><br />
	
		<form name="login-form" action="" method="post">
			<label for="email-field">E-mail</label><input type="email" name="email" id="email-field" /><br />
			<label for="pass-field">Password</label><input type="password" name="password" id="pass-field" /><br />
			<!--label for="remember-field">Stay connected</label><input type="checkbox" name="remember" id="remember-checkbox" /><br /-->
			<input type="submit" value="Send" />
		</form>
		<br /><a href="recoverPassword.php">Forgot password?</a>
	</body>
</html>
