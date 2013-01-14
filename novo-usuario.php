<?php
require 'includes/autoloader.php';

use core\Database;
use core\Users;

if (isset($_POST['name'])) {	
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($name) && !empty($email) && !empty($password)) {
        try {
            $database = new Database();
            $registered = Users::isEmailRegistered($email, $database);

            if (!$registered) {
                $created = Users::addUser($name, $email, $password, $database);
            }

            $database = null;
        } catch (PDOException $e) {
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title>Novo usu&aacute;rio | EACHe sua rep&uacute;blica</title>

        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
	    <link rel="stylesheet" type="text/css" href="css/basico-minified.css"/>
        <link rel="canonical" href="http://eacherepublica.com.br/novo-usuario" />
    </head>

	<body>
        <div id="wrapper">
            <header>
                <a href="http://eacherepublica.com.br/"><h1>EACH sua rep&uacute;blica</h1></a> 
                <p><a href="http://eacherepublica.com.br/">&#8592; P&aacute;gina inicial</a></p>
            </header>

            <div id="content">
		        <!-- Alerts -->
                <?php if (isset($registered) && $registered) : ?>
                    <div class="alert alert-error">
                        <b>Abre o olho!</b> Esse e-mail j&aacute; est&aacute; registrado! Tente: <a href="http://eacherepublica.com.br/esqueci-minha-senha">Esqueci minha senha</a>.
                    </div>
                <?php endif; ?>

                <?php if (isset($created)) : if ($created) : ?>
                    <div class="alert alert-success">
                        <b>Eba!</b> Agora voc&ecirc; &eacute; um dos nossos! Comece j&aacute;: insira as rep&uacute;blicas que voc&ecirc; conhece!
                    </div>
                <?php else : ?>
                    <div class="alert alert-error">
                        <b>Ah!</b> Algum erro ocorreu e seu cadastro n&atilde;o p&ocirc;de ser finalizado. Tente novamente mais tarde.
                    </div>
                <?php endif; endif; ?>
                <!--/ Alerts -->

                <h2>Novo usu&aacute;rio</h2>

                <form name="registry-form" action="" method="post" onsubmit="return verifyUserRegistration();">		
                    <label for="name-field" class="required">Nome</label> <span class="label label-important">Obrigat&oacute;rio</span>
                    <input type="text" name="name" id="name-field" class="input-block-level" required /><br />

                    <label for="email-field" class="required">E-mail</label> <span class="label label-important">Obrigat&oacute;rio</span>
                    <input type="email" name="email" id="email-field"  class="input-block-level" required /><br />

			        <label for="pass-field" class="required">Senha</label> <span class="label label-important">Obrigat&oacute;rio</span>
                    <input type="password" name="password" id="pass-field"  class="input-block-level" required /><br />

			        <label for="confirm-pass-field" class="required">Confirma&ccedil;&atilde;o da senha</label> <span class="label label-important">Obrigat&oacute;rio</span>
                    <input type="password" id="confirm-pass-field" class="input-block-level" required /><br />

                    <button type="submit" class="btn btn-info">Registrar</button>
		        </form>
		    </div>

        </div>

        <script type="text/javascript" src="js/verify-form-minified.js"></script>

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
