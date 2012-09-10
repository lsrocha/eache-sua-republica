<?php
session_start();

require 'includes/autoloader.php';

use core\users\Authentication;

if (!Authentication::isLoggedIn()) {
    header('Location: index.php?goback='.$_SERVER['PHP_SELF']);
}
?>

<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Secret</title>
	</head>
	<body>
		<a href="index.php?logout=1">Sign out</a>
		<h1>Welcome!</h1>
		<img src="surprised-child.jpg" width="600" height="400" alt="surpriced face" />
	</body>
</html>
