/**
 * Author: Lucas Proni
 */

var marker;
var map;
var each = new google.maps.LatLng(-23.485565,-46.502963);
var latlng_sw = new google.maps.LatLng(-23.917853,-47.067604);//Juquitiba = Limite inferior da RMSP
var latlng_ne = new google.maps.LatLng(-23.325863,-45.912094);//Ponto em Jacarei = Limite superior da RMSP
var bounds    = new google.maps.LatLngBounds(latlng_sw, latlng_ne);
var verificou_mapa = false;

function validateForm()
{	
    //E-mail
    var x=document.forms["abc"]["email"].value;
    var atpos=x.indexOf("@");
    var dotpos=x.lastIndexOf(".");
    if ((x!=0)&&(atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length))
    {
        alert("Insira um email válido!");
        return false;
    } else return verificou_mapa;
}	

//Telefone
function mascara(o,f){  
    v_obj=o  
    v_fun=f  
    setTimeout("execmascara()",1)  
}  
function execmascara(){  
    v_obj.value=v_fun(v_obj.value)  
}  
function mtel(v){  
    v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito  
    v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos  
    v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos  
    return v;  
}  
function id( el ){  
    return document.getElementById( el );  
}  

window.onload = function(){  
    id('telefone').onkeypress = function(){  
        mascara( this, mtel );  
    }
    //inicializar();
}

function inicializar(){
    var prop_map = {
        center: each,
        zoom:14,
        mapTypeId:google.maps.MapTypeId.ROADMAP
	};

    map = new google.maps.Map(document.getElementById('canvas'),prop_map);
}
google.maps.event.addDomListener(window, 'load', inicializar);	

//Preco
function MascaraMoeda(objTextBox, SeparadorMilesimo, SeparadorDecimal, e){
    var sep = 0;
    var key = '';
    var i = j = 0;
    var len = len2 = 0;
    var strCheck = '0123456789';
    var aux = aux2 = '';
    var whichCode = (window.Event) ? e.which : e.keyCode;
	
    if (whichCode == 13) return true;
    key = String.fromCharCode(whichCode); // Valor para o código da Chave
    if (strCheck.indexOf(key) == -1) return false; // Chave inválida
    len = objTextBox.value.length;
    for(i = 0; i < len; i++)
        if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal)) break;
    aux = '';
    for(; i < len; i++)
        if (strCheck.indexOf(objTextBox.value.charAt(i))!=-1) aux += objTextBox.value.charAt(i);
    aux += key;
    len = aux.length;
    if (len == 0) objTextBox.value = '';
    if (len == 1) objTextBox.value = '0'+ SeparadorDecimal + '0' + aux;
    if (len == 2) objTextBox.value = '0'+ SeparadorDecimal + aux;
    if (len > 2) {
        aux2 = '';
        for (j = 0, i = len - 3; i >= 0; i--) {
            if (j == 3) {
                aux2 += SeparadorMilesimo;
                j = 0;
            }
            aux2 += aux.charAt(i);
            j++;
        }

        objTextBox.value = '';
        len2 = aux2.length;
        for (i = len2 - 1; i >= 0; i--)
        objTextBox.value += aux2.charAt(i);
        objTextBox.value += SeparadorDecimal + aux.substr(len - 2, len);
    }
    return false;
}

//NA HORA QUE COLOCO ISSO, AS MÁSCARAS SOMEM

function mostraEndereco(){
    var geocoder = new google.maps.Geocoder();
    alert("estah aqui");
    var enderecoo = document.getElementById("endereco").value;
    geocoder.geocode( { 'address': enderecoo}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) 
	    {
            document.getElementById("lat").value = results[0].geometry.location.lat();
            //alert(document.getElementById("lat").value = results[0].geometry.location.lat(););
            document.getElementById("lng").value = results[0].geometry.location.lng();
        } else {
			//alert("Desculpe, mas nao foi possivel recuperar a Geolocalizacao do endereco: '"+enderecoo+"'\nStatus do Erro: "+ status);
        }
	});

	//return;
}

function geocodificar(){
    var geocoder = new google.maps.Geocoder();
    var address = document.forms["abc"]["endereco"].value;

    if(address!=""){
        document.getElementById("botao_enviar").style.display="none";
        geocoder.geocode({'address': address}, function(results, status){
            if(status==google.maps.GeocoderStatus.OK){
                var latlng = results[0].geometry.location;
                if (!bounds.contains(latlng)){
                    alert("Insira um endereço válido.");
                    return;
                } else {
                    document.getElementById("lat").value = latlng.lat();
                    document.getElementById("lng").value = latlng.lng();

                    marker = new google.maps.Marker({
                        map: map,
                        position: latlng,
                        animation: google.maps.Animation.DROP,
                        icon:"img/mapa-marcador-50-sombra.png",
                        title:address
					});

                    map.panTo(latlng);
                    document.getElementById("confirmacao").style.display="block";
                }
            } else {
                    document.getElementById("botao_enviar").style.display="inline-block";
	                alert("Endereço não encontrado. Verifique a corretude da grafia, a rua (ou Avenida, ou Alameda...), o bairro. Não acrescente o CEP.");
                }
            });
    } else alert("Insira um endereço válido.");
}

function confirmar(){
    document.getElementById("confirmacao").style.display = "none";
    verificou_mapa = true;
    document.getElementById("envia_form").style.display = "inline";
}

function negar(){
    map.panTo(each);
    document.forms["dados_republicas"]["endereco"].value = "";
    document.getElementById("confirmacao").style.display = "none";
    document.getElementById("botao_enviar").style.display="inline";
    document.forms["dados_republicas"]["endereco"].focus();
}
