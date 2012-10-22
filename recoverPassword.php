<?php
require 'includes/autoloader.php';

use core\Users;

if (isset($_GET['email']) && isset($_GET['token']) && isset($_POST['new_password'])) {
    $email = $_GET['email'];
    $password = $_POST['new_password'];
    $token = $_GET['token'];    

    $user = new Users();
    $user->createNewPassword($email, $password, $token);

    echo 'Password successfully created!';
} elseif (isset($_POST['email'])) {
    $email = $_POST['email'];
    $user = new Users();

    if ($user->isEmailRegistered($email)) {
        $token = $user->generateRecoveryToken($email);

        $message = <<<EOD
Hey,\n 
Click on the link below to create your new password: 
\n http://leorocha.com/republicas/recoverPassword.php?email={$email}&token={$token}
EOD;
        mail($email, '[Republicas] Recover password', $message);

        echo "We've sent a e-mail to you. Check your inbox, baby!";
    } else {
        echo 'This e-mail is not in our database!';
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Recover password</title>
    </head>
    <body>
        <form action="" method="post">
            <?php if (isset($_GET['email']) && isset($_GET['token'])) : ?>
                <label>New password</label><input type="password" name="new_password" />
            <?php else : ?>
                <label>E-mail</label><input type="email" name="email" />
            <?php endif; ?>
            <input type="submit" value="Send" />
        </form>

        <br /><a href="index.php">Back</a>
    </body>
</html>
