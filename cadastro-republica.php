<?php
session_start();

require 'includes/autoloader.php';

use core\Republicas; 
use core\Authentication;

$isLoggedIn = Authentication::isLoggedIn();

if (isset($_POST{'name'})) {
    $republicas = new Republicas();
    $success = $republicas->addRepublica($_POST, $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Cadastro de rep&uacute;blica | EACHe sua rep&uacute;blica</title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
	    <link rel="stylesheet" type="text/css" href="css/cadastro-republica-minified.css"/>
        <link rel="canonical" href="http://eacherepublica.com.br/cadastro-republica" />
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
                        <b>Uhul!</b> Rep&uacute;blica cadastrada com sucesso!
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">
                        <b>Bah!</b> N&atilde;o foi poss&iacute;vel registrar essa rep&uacute;blica.
                    </div>
                <?php endif; endif; ?>
                
                
                <h2>Cadastro de rep&uacute;blica</h2>
                
                <?php if (!$isLoggedIn) : ?>
                    <div class="alert alert-error">
                        <b>Ei!</b> &Eacute; preciso estar logado para cadastrar rep&uacute;blicas! <a href="http://eacherepublica.com.br/">Acesse sua conta</a> na p&aacute;gina inicial.
                    </div>
                <?php else : ?>

                    <form name="abc" id="dados_republicas" action="" method="POST" onsubmit="return validateForm();" >
                        <label class="required">Nome da rep&uacute;blica</label>  <span class="label label-important">Obrigat&oacute;rio</span>
                        <input required  type="text" name="name" size="25" maxlength="35" class="input-block-level" /><br />
                    
                        <label class="required">Endere&ccedil;o</label> <span class="label label-important">Obrigat&oacute;rio</span>
                        <input required type="text" name="address" size="60" id="endereco" value="" class="input-block-level" /><br />
                        <input type="hidden" name="latitude" id="lat" />
                        <input type="hidden" name="longitude" id="lng" />
                    
                        <label>Telefone</label>
                        <input type="text" name="phone" id="telefone" maxlength="15" class="input-block-level" /><br />
                    
                        <label>E-mail</label>
                        <input type="text" name="email" class="input-block-level" /><br />
                    
                        <label>Quarto</label>
                        <select name="vacancy_type">
                            <option value="null"></option>
                            <option value="Dividido">Compartilhado</option>
                            <option value="Individual">Individual</option>
                        </select><br />
                        
                        <label>Tipo de rep&uacute;blica</label>
                        <select name="gener">
                            <option value="null">
                            <option value="Masculino">Masculino</option>
                            <option value="Feminina">Feminina</option>
                            <option value="Mista">Mista</option>
                        </select><br />
                    
                        <label>Pre&ccedil;o</label>
                        <input type="text" name="price" onKeyPress="return(MascaraMoeda(this,'.',',',event));" /><br />
                    
                        <label>N&uacute;mero de Moradores</label>
                        <input type="number" min="0" max="100" name="num_dwellers" value="0" class="input-mini" /><br />
                    
                        <label>N&uacute;mero de Vagas</label>
                        <input type="number" min="0" max="100" name="num_vacancies" value="0" class="input-mini" /><br />
                    
                        <label>Mais Informa&ccedil;&otilde;es</label>
                        <textarea id="more" rows =3 cols = 70 placeholder="Digite aqui mais informa&ccedil;&otilde;es" name="info_adicional" size ="60" maxlength="2400" class="input-block-level"></textarea>
                    
                        <div id="canvas"></div>
                        <div id="confirmacao">
                            <p>O endere&ccedil;o est&aacute; correto?</p>
                            <button type="button" onclick="confirmar();" class="btn btn-success">Sim</button>
                            <button type="button" onclick="negar();" class="btn btn-danger">N&atilde;o</button>
                        </div>
                    
                        <button id="botao_enviar" type="button" onclick="geocodificar();" class="btn btn-success">Confirmar endere&ccedil;o</button>
                        <input id="envia_form" style="display: none;" type="submit" value="Cadastrar rep&uacute;blica" class="btn btn-info" />
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript" src="js/republica-registry-minified.js"></script>
        
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
