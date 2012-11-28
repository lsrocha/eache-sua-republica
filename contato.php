<?php
if (isset($_POST['name'])) {
    $nameField = $_POST['name'];
    $emailField = $_POST['email'];
    $message = $_POST['message'];

    $success = false;

    if (!empty($nameField) && !empty($emailField) && !empty($message)) {
        $nameField = filter_var($nameField, FILTER_SANITIZE_STRING);
        $emailField = filter_var($emailField, FILTER_SANITIZE_EMAIL);
        $message = filter_var($message, FILTER_SANITIZE_STRING);

        $to = 'Leonardo Rocha <leonardo.lsrocha@gmail.com>';
        $subject = '[EACHe sua república] Formulário Contato';
        $additionalHeader = "{$nameField} <{$emailField}>";

        $success = mail($to, $subject, $message, $additionalHeader);
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title>Contato | EACHe sua rep&uacute;blica</title>

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
                <?php if (isset($success)) : if ($success) : ?>
                    <div class="alert alert-success">
                        <b>Tudo certo!</b> Mensagem enviada com sucesso.
                    </div>
                <?php else : ?>
                    <div class="alert alert-error">
                        <b>Bah!</b> N&atilde;o foi poss&iacute;vel enviar a mensagem.
                    </div>
                <?php endif; endif; ?>

		        <h2>Contato</h2>

                <form name="registry-form" action="" method="post" onsubmit="return verifyContactForm();">		
                    <label for="name-field" class="required">Nome</label> <span class="label label-important">Obrigat&oacute;rio</span>
                    <input type="text" name="name" id="name-field" class="input-block-level" required /><br />

                    <label for="email-field" class="required">E-mail</label> <span class="label label-important">Obrigat&oacute;rio</span>
                    <input type="email" name="email" id="email-field"  class="input-block-level" required /><br />

                    <label for="message-box" class="required">Mensagem</label> <span class="label label-important">Obrigat&oacute;rio</span>
                    <textarea name="message" id="message-box" rows="10" columns="55" class="input-block-level" required></textarea>

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
