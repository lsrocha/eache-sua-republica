<?php
require 'includes/autoloader.php';

use core\Users;

if (isset($_POST['name'])) {	
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($name) && !empty($email) && !empty($password)) {
        $user = new Users();

        if ($user->isEmailRegistered($email)) {
            echo 'This e-mail is already registered!';
        } else {	
            $result = $user->addUser($name, $email, $password);

            if ($result) {
                echo 'User successfully created!';
            } else {
                echo 'Error. Try again!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Registry</title>
	</head>
	<body>
		<form name="registry-form" action="" method="post">		
			<label for="name-field">Name</label><input type="text" name="name" id="name-field" /><br />
			<label for="email-field">E-mail</label><input type="email" name="email" id="email-field" /><br />
			<label for="pass-field">Password</label><input type="password" name="password" id="pass-field" /><br />
			<label for="confirm-pass-field">Confirm password</label><input type="password" id="confirm-pass-field" /><br />
			<input type="submit" value="Register" />
		</form>
		
		<br /><a href="index.php">Back</a>
	</body>
</html>
