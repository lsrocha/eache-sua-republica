<?php
require 'includes/autoloader.php';

use core\Users;

if (isset($_GET['email']) && isset($_GET['token']) && isset($_POST['new_password'])) {
    $email = $_GET['email'];
    $password = $_POST['new_password'];
    $token = $_GET['token'];    

    $user = new Users();
    $created = $user->createNewPassword($email, $password, $token);

} elseif (isset($_POST['email'])) {
    $email = $_POST['email'];
    $user = new Users();

    $registered = $user->isEmailRegistered($email);

    if ($registered) {
        $token = $user->generateRecoveryToken($email);

        $message = <<<EOD
Hey,\n 
Clique no link abaixo para criar uma nova senha:

\n http://eacherepublica.com.br/esqueci-minha-senha?email={$email}&token={$token}
EOD;
        $sent = mail($email, '[EACHE sua república] Esqueci minha senha', $message);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Esqueci minha senha | EACHe sua rep&uacute;blica</title>

        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
	    <link rel="stylesheet" type="text/css" href="css/basico-minified.css"/>
        <link rel="canonical" href="http://eacherepublica.com.br/esqueci-minha-senha" />
    </head>

    <body>
        <div id="wrapper">
            <header>
                <a href="http://eacherepublica.com.br/"><h1>EACH sua rep&uacute;blica</h1></a> 
                <p><a href="http://eacherepublica.com.br/">&#8592; P&aacute;gina inicial</a></p>
            </header>

            <div id="content">
                <!-- Alerts -->
                <?php if (isset($sent)) : if ($sent) : ?>
                    <div class="alert alert-success">
                        <b>Hey!</b> N&oacute;s enviamos um e-mail para voc&ecirc;! Cheque sua inbox, baby!
                    </div>
                <?php else : ?>
                    <div class="alert alert-error">
                        <b>Bah!</b> Tentamos te enviar um e-mail, mas houve algum problema.
                    </div>                   
                <?php endif; endif; ?>

                <?php if (isset($created) && !$created) : ?>
                    <div class="alert alert-error">
                        <b>Droga!</b> Ocorreu algum problema e sua senha n&atilde;o p&ocirc;de ser cadastrada.
                    </div>
                <?php endif; ?>

                <?php if (isset($registered) && !$registered) : ?>
                    <div class="alert alert-error">
                        <b>Senta l&aacute;, Cl&aacute;udia!</b> Esse e-mail fornecido n&atilde;o consta em nosso banco de dados. Estamos de olho!
                    </div>
                <?php endif; ?>
                <!--/ Alerts -->

		        <h2>Esqueci minha senha</h2>
                <form action="" method="post">
                    <?php if (isset($_GET['email']) && isset($_GET['token'])) : ?>
                        <label>Nova senha</label>
                        <input type="password" name="new_password" class="input-block-level" />
                        <br />
                        <input type="submit" value="Gravar" class="btn btn-info" />
                    <?php else : ?>
                        <label>E-mail</label>
                        <input type="email" name="email" class="input-block-level" />
                        <br />
                        <input type="submit" value="Enviar" class="btn btn-info" />
                    <?php endif; ?>

                </form>
            </div>

        </div>
        
        <!-- Google Analytics -->
        <script type="text/javascript">
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-29750007-3']);
            _gaq.push(['_setDomainName', 'eacherepublica.com.br']);
            _gaq.push(['_trackPageview']);
        
            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
        </script>
        <!--/ Google Analytics -->
    </body>
</html>
