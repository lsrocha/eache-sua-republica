<?php
require 'includes/autoloader.php';

use core\Feedback;

if (isset($_POST['dificuldade'])) {
    $feedback = new Feedback();
    $success = $feedback->save($_POST);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <title>Feedback | EACHe sua rep&uacute;blica</title> 

        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />

        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
	    <link rel="stylesheet" type="text/css" href="css/feedback-minified.css"/>
        <link rel="canonical" href="http://eacherepublica.com.br/feedback" />
    </head>

    <body>
        <div id="wrapper">
            <header>
                <a href="http://eacherepublica.com.br/"><h1>EACH sua rep&uacute;blica</h1></a> 
                <p><a href="http://eacherepublica.com.br/">&#8592; P&aacute;gina inicial</a></p>
            </header>

            <div id="content">
	            <!-- Alerts -->
                <?php if (isset($success)) : if ($success) : ?>
                    <div class="alert alert-success">
                        <b>Excelente!</b> Sua opini&atilde;o foi registrada com sucesso. Agora, que tal indicar nosso site para seus amigos?
                    </div>

                    <div class="g-plusone" data-size="medium" data-annotation="none" data-href="http://eacherepublica.com.br/"></div>
                    <a href="https://twitter.com/share" class="twitter-share-button" data-lang="pt" data-count="none" data-url="http://eacherepublica.com.br/">Tweetar</a>
                    <div class="fb-like" data-href="http://eacherepublica.com.br/" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false" data-font="verdana"></div>
                    <br /><br /> 
                    <?php else : ?>
                    <div class="alert alert-error">
                        <b>Ah!</b> N&atilde;o foi poss&iacute;vel gravar os dados do formul&aacute;rio. 
                    </div>
                <?php endif; endif; ?>
                <!--/ Alerts -->
                
                <h2>Feedback</h2>

                <form name="feedback" id="feedback" action="" method="post">
		            <p><b>Voc&ecirc; teve dificuldade em alguma &aacute;rea do site?</b></p>
                    <input type="radio" name="dificuldade" value="sim" onclick="show();" /><label>Sim</label>
                    <input type="radio" name="dificuldade" value="nao" onclick="hide();" /><label>N&atilde;o</label>
                    <br />
                    
                    <textarea placeholder ="Desculpe o transtorno. Explique-nos qual foi a dificuldade enfrentada." id="area" style="display: none;" name="explicacao_dificuldade" rows="3" cols="50" class="input-block-level"></textarea>

                    <p><b>Voc&ecirc; encontrou o que procurava?</b></p>
                    <input type="radio" name="encontrou" value="sim" /><label>Sim</label>
                    <input type="radio" name="encontrou" value="nao" /><label>N&atilde;o</label>
                    <br />

                    <p><b>Voc&ecirc; &eacute; aluno da </b><strong>EACH - USP</strong><b>?</b></p>
                    <input type="radio" name="aluno" value="sim" /><label>Sim</label>
                    <input type="radio" name="aluno" value="nao" /><label>N&atilde;o</label>
                    <br />

                    <p><b>Voc&ecirc; indicaria o site a terceiros?</b></p>
                    <input type="radio" name="indicaria" value="sim" /><label>Sim</label>
                    <input type="radio" name="indicaria" value="nao" /><label>N&atilde;o</label><br />

                    <p><b>Por onde voc&ecirc; encontrou o </b><strong>EACHe sua rep&uacute;blica?</strong></p>
                    <select name="referencia" class="input-xlarge">
                        <option value="null"></option>
                        <option value="Facebook">Facebook</option>
                        <option value="amigos ou familiares">Indica&ccedil;&atilde;o de amigos ou familiares</option>
                        <option value="site do DASI">Site do DASI</option>
                        <option value="site da each">Site da EACH</option>
                        <option value="Twitter">Twitter</option>
                        <option value="outros">Outros</option>
                    </select>
                    <br />

                    <p><b>Em uma escala de 0 a 5, avalie o site de acordo com os seguintes aspectos:</b></p>

                    <p>Design</p>
                    <div class="rating">
                        <label>0</label><br />
                        <input type="radio" name="design" value="0" />
                    </div>

                    <div class="rating">
                        <label>1</label><br />
                        <input type="radio" name="design" value="1" />
                    </div>

                    <div class="rating">
                        <label>2</label><br />
                        <input type="radio" name="design" value="2" />
                    </div>

                    <div class="rating">
                        <label>3</label><br />
                        <input type="radio" name="design" value="3" />
                    </div>

                    <div class="rating">
                        <label>4</label><br />
                        <input type="radio" name="design" value="4" />
                    </div>

                    <div class="rating">
                        <label>5</label><br />
                        <input type="radio" name="design" value="5" />
                    </div>

                    <p>Funcionalidades</p>
                    <div class="rating">
                        <label>0</label><br />
                        <input type="radio" name="funcionalidades" value="0" />
                    </div>

                    <div class="rating">
                        <label>1</label><br />
                        <input type="radio" name="funcionalidades" value="1" />
                    </div>

                    <div class="rating">
                        <label>2</label><br />
                        <input type="radio" name="funcionalidades" value="2" />
                    </div>

                    <div class="rating">
                        <label>3</label><br />
                        <input type="radio" name="funcionalidades" value="3" />
                    </div>

                    <div class="rating">
                        <label>4</label><br />
                        <input type="radio" name="funcionalidades" value="4" />
                    </div>

                    <div class="rating">
                        <label>5</label><br />
                        <input type="radio" name="funcionalidades" value="5" />
                    </div>

                    <p>Acessibilidade</p>
                    <div class="rating">
                        <label>0</label><br />
                        <input type="radio" name="acessibilidade" value="0" />
                    </div>

                    <div class="rating">
                        <label>1</label><br />
                        <input type="radio" name="acessibilidade" value="1" />
                    </div>

                    <div class="rating">
                        <label>2</label><br />
                        <input type="radio" name="acessibilidade" value="2" />
                    </div>

                    <div class="rating">
                        <label>3</label><br />
                        <input type="radio" name="acessibilidade" value="3" />
                    </div>

                    <div class="rating">
                        <label>4</label><br />
                        <input type="radio" name="acessibilidade" value="4" />
                    </div>

                    <div class="rating">
                        <label>5</label><br />
                        <input type="radio" name="acessibilidade" value="5" />
                    </div>

                    <p>Inser&ccedil;&atilde;o de rep&uacute;blicas</p>
                    <div class="rating">
                        <label>0</label><br />
                        <input type="radio" name="inserir_rep" value="0" />
                    </div>                    

                    <div class="rating">
                        <label>1</label><br />
                        <input type="radio" name="inserir_rep" value="1" />
                    </div>

                    <div class="rating">
                        <label>2</label><br />
                        <input type="radio" name="inserir_rep" value="2" />
                    </div>

                    <div class="rating">
                        <label>3</label><br />
                        <input type="radio" name="inserir_rep" value="3" />
                    </div>

                    <div class="rating">
                        <label>4</label><br />
                        <input type="radio" name="inserir_rep" value="4" />
                    </div>

                    <div class="rating">
                        <label>5</label><br />
                        <input type="radio" name="inserir_rep" value="5" />
                    </div>

                    <p><b>Encontrou alguma informa&ccedil;&atilde;o errada a respeito de uma </b><strong>rep&uacute;blica</strong><b>? Fale conosco:</b></p>
                    <textarea id="more" rows="2" cols="60" name="info_adicional" size ="50" maxlength="2400" class="input-block-level"></textarea>

                    <input type="submit"  value="Enviar" class="btn btn-info" />
                </form>
            </div>
        </div>

        <script type="text/javascript">
            function show() { document.getElementById('area').style.display = 'block'; }
            function hide() { document.getElementById('area').style.display = 'none'; }
        </script>

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
