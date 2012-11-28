<?php
session_start();

require 'includes/autoloader.php';

use core\Authentication;

$isLoggedIn = Authentication::isLoggedIn();

if ($isLoggedIn) {
    if (isset($_GET['logout']) && $_GET['logout']) {
        Authentication::logout();
        header('Location: /');
    } 
} elseif (isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $isLoggedIn = Authentication::login($email, $password); 
    }		
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>EACHe sua rep&uacute;blica | Rep&uacute;blicas pr&oacute;ximas &agrave; EACH USP, na zona leste de S&atilde;o Paulo</title>

        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="description" content="Encontre rep&uacute;blicas na regi&atilde;o da EACH, USP leste. O EACHE sua rep&uacute;blica permite que voc&ecirc; localize moradias estudant&iacute;s na zona leste de S&atilde;o Paulo." />
        
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <link href="css/home-minified.css" rel="stylesheet" type="text/css" />
        <link rel="canonical" href="http://eacherepublica.com.br/" />
    </head>

    <body onload="carregar();">
        <div id="wrapper">

            <!-- MODAL LOGIN -->
            <div id="form-login" class="modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Login</h3>
                </div>

                <div class="modal-body">
                    <form action="" method="post" class="form-horizontal" name="login-form">

                        <div class="control-group">
                            <label class="control-label" for="inputEmail">E-mail</label>
                            <div class="controls">
                                <input type="text" name="email" id="inputEmail" placeholder="Email" required />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="inputPassword">Senha</label>
                            <div class="controls">
                                <input type="password" name="password" id="inputPassword" placeholder="Senha" required />
                            </div>
                        </div>

                        <div class="controls">
                            <a href="http://eacherepublica.com.br/esqueci-minha-senha">Esqueci minha senha</a>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
				    <button data-dismiss="modal" aria-hidden="true" class="btn">Fechar</button>
				    <a href="javascript:login();" class="btn btn-info">Entrar</a>
				</div>
            </div>
		    <!--/ MODAL LOGIN -->

            <!-- MODAL ADVANCED SEARCH -->
            <div id="advanced-search" class="modal hide fade">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3>Pesquisa avan&ccedil;ada</h3>
                </div>

                <div class="modal-body">
                    <form class="form-horizontal">
                    
                        <div class="control-group">
                            <label class="control-label">Raio de pesquisa</label>
                        
                            <div class="controls">
                                <select id="distancia_prox" name="distancia_prox" disabled="disabled">
                                    <option value=100000></option>
                                    <option value=1>1 km.</option>
                                    <option value=2>2 km.</option>
                                    <option value=5>5 km.</option>
                                    <option value=10>10 km.</option>
                                    <option value=15>15 km.</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Nome da rep&uacute;blica</label>

                            <div class="controls">
                                <input id="pesquisar_nome" name="pesquisar_nome" type="text" value="" size="40"/>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Valor mensal</label>

                            <div class="controls">
                                <select id="aluguel" name="aluguel">
                                    <option value=100000></option>
                                    <option value=50>Até R$50,00</option>
                                    <option value=100>Até R$100,00</option>
                                    <option value=150>Até R$150,00</option>
                                    <option value=200>Até R$200,00</option>
                                </select>
                            </div>
                        </div>
 
                        <div class="control-group">
                            <label class="control-label">Tipo</label>

                            <div class="controls">
                                <select id="tipo" name="tipo">	
                                    <option value=100000></option>
                                	<option value="masculina">Masculina</option>
                                	<option value="feminina">Feminina</option>
                                	<option value="mista">Mista</option>
                                </select>
                            </div>
                        </div>
	
                        <div class="control-group">
	                        <label class="control-label">Distância até a EACH</label>

                            <div class="controls">
                                <select id="distancia_each" name="distancia" >
                                	<option value=100000></option>
                                	<option value=300>Até 300m</option>
                                	<option value=500>Até 500m</option>
                                	<option value=1000>Até 1km</option>
                                	<option value=2000>Até 2km</option>
                                	<option value=5000>Até 5km</option>
                                	<option value=10000>Até 10km</option>
                                	<option value=15000>Até 15km</option>
                                </select>
                            </div>
                        </div> 
                    </form>
                </div>

                <div class="modal-footer">
				    <button data-dismiss="modal" aria-hidden="true" class="btn">Fechar</button>
				    <button data-dismiss="modal" aria-hidden="true" class="btn btn-info" onclick="pesquisar();">Buscar</button>
				</div>
            </div>
            <!--/ MODAL ADVANCED SEARCH -->
                
            <header>
                <h1>EACHe sua rep&uacute;blica</h1>
                
                <div id="header-right"> 
                    
                    <div id="user">
                        <?php if (!$isLoggedIn) : ?>
                            <a href="#form-login" role="button" class="btn" data-toggle="modal">Login</a>
                            <a href="http://eacherepublica.com.br/novo-usuario" class="btn">Cadastre-se</a>
                        <?php else : ?>
                            <a href="/?logout=1" class="btn btn-info">Sair</a>
                        <?php endif; ?>

                    </div>
                    
                    <div id="search-options" class="input-append">
                        <input class="search-box-input" id="endereco_prox" type="text" placeholder="Digite o endere&ccedil;o ou a localiza&ccedil;&atilde;o aproximada" name="endereco_prox" onblur="habilitarOuDesabilitarDist();"/>
                        <button class="btn" type="button" onclick="pesquisar();"> Buscar </button>
   		                <a href="http://eacherepublica.com.br/cadastro-republica" class="btn btn-primary">
                            <i class="icon-map-marker icon-white"></i> Insira rep&uacute;blica
                        </a>	
                    </div>
	                
                    <a href="#advanced-search" data-toggle="modal">Pesquisa avan&ccedil;ada</a>
                    <span class="circle-separator">&#9899;</span>
                    <a href="javascript:resetarMapa();">Limpar filtros</a>
                </div>                   
            </header>

            <?php if (isset($loginSuccess) && !$loginSuccess) : ?>
                <div class="alert alert-error">
                    <b>Aten&ccedil;&atilde;o:</b> N&atilde;o foi poss&iacute;vel fazer login. E-mail e senha n&atilde;o correspondem.
                </div>
            <?php endif; ?>
            
            <div id="social-networks">
                <div class="g-plusone" data-size="medium" data-annotation="none" data-href="http://eacherepublica.com.br/"></div>
                <a href="https://twitter.com/share" class="twitter-share-button" data-lang="pt" data-count="none" data-url="http://eacherepublica.com.br/">Tweetar</a>
                <div class="fb-like" data-href="http://eacherepublica.com.br/" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false" data-font="verdana"></div>
            </div>
            
            <div class="alert alert-info">
                <strong>Feedback:</strong>
                estamos interessados em saber como est&aacute; sendo sua experi&ecirc;ncia.
                Ajude-nos enviando <a href="http://eacherepublica.com.br/feedback" title="Feedback">sua opini&atilde;o</a>.
            </div>

            <!-- MAPA -->
            <div id="google_map" style="width: 838px; height: 576px;"></div>
            <!--/ MAPA -->

            <footer>
                <nav>
                    <ul class="navbar">
                        <li>> <a href="http://eacherepublica.com.br/sobre">Sobre o Projeto</font></a> </li>
                        <li>> <a href="http://eacherepublica.com.br/feedback">Feedback</a> </li>
                        <li>> <a href="http://eacherepublica.com.br/contato">Contato</a> </li>
                    </ul>
                </nav>

                <a href="https://github.com/LRocha94/EACHe-sua-republica">
                    <img src="img/logo-github.png" alt="Github" width="120" height="53" />
                </a>

            </footer>
        </div>

        <script type="text/javascript">
            function login() { document.forms['login-form'].submit(); }
        </script>
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=geometry&sensor=false"></script>
        <script type="text/javascript" src="js/maps-minified.js"></script>
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script src="js/bootstrap.min.js"></script>
        
        <!-- Google Plus One -->
        <script type="text/javascript">
            window.___gcfg = {lang: 'pt-BR'};
        
            (function() {
                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                po.src = 'https://apis.google.com/js/plusone.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
            })();
        </script>
        <!--/ Google Plus -->
        
        <!-- Twitter -->
        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
        <!--/ Twitter -->
        
        <!-- Facebook -->
        <div id="fb-root"></div> 
        <script>
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId=115164028643627";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <!--/ Facebook -->
        
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
