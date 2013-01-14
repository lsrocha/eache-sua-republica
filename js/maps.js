/**
 * Busca e insercao de dados no mapa
 *
 * Google Maps API
 *
 * Author: Renan Souza de Freitas
 */

var map;
var markers = new Array();
var encontradas;
var geocoder;
var markerPesquisado;
var lat_each = -23.485565;
var lng_each = -46.502963;
var latlng;
var name;
var address;

var valores;
var caminhos;
var pesquisado = false;
var centro = new google.maps.LatLng(lat_each, lng_each);
var valor_default = 100000;
var pesquisadoAoRedor = false;
var deve_rodar = false;
var latlng_pesquisado = new google.maps.LatLng(lat_each, lng_each);
var pesquisa_nome = false;
var exibir_nome;

var latlng_zoom = new Array();
var i_z;
var latlng_sw = new google.maps.LatLng(-23.917853,-47.067604); //Juquitiba = Limite inferior da RMSP
var latlng_ne = new google.maps.LatLng(-23.325863,-45.912094); //Ponto em Jacarei = Limite superior da RMSP
var bounds    = new google.maps.LatLngBounds(latlng_sw, latlng_ne);

//INICIALIZACAO DO MAPA
function carregar(){
    initialize();
    pegaRep(lat_each, lng_each, valor_default); //Carrega a partir da EACH, todas as reps existentes
}

function initialize() {
    var prop_map = {
		center:centro,
		zoom:14,
		mapTypeId:google.maps.MapTypeId.ROADMAP
	};

    map = new google.maps.Map(document.getElementById('google_map'),prop_map);
	geocoder = new google.maps.Geocoder();
}

google.maps.event.addDomListener(window, 'load', initialize);	

function pegaRep(latitude, longitude, raio){ //Essa funcao nao eh chamada apenas na inicializacao/ Seu raio eh em km
	var xmlhttp;
	var result;
	var url = "/republicas/republicas.php?lat="+latitude+"&lng="+longitude+"&r="+raio;

	if (window.XMLHttpRequest){ // IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else { // IE6, IE5		
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

    xmlhttp.open('GET',url,true);
	xmlhttp.send();
	xmlhttp.onreadystatechange=function(){		
        if (xmlhttp.readyState==4){
		    if (xmlhttp.status==200){
			    valores = eval("("+ xmlhttp.responseText +")");
			    marcaRep(valores);
			    //document.getElementById('json').innerHTML=xmlhttp.responseText;
		    }
		
            if (xmlhttp.status==404){
			    document.write("Not Found");
		    }
	    }
	}
}

//FUNCOES DE MARCACAO DE DADOS NO MAPA

function marcaRep(valores){
	var name, address, latlng, distance, distance_ate_each;
	//markers=new Array();
	encontradas=0;
	var pesquisar_nome = document.getElementById("pesquisar_nome").value;
	var aluguel = document.getElementById("aluguel").value;
	var tipo = document.getElementById("tipo").value;
	var distancia_prox = document.getElementById("distancia_prox").value;

	for (var i = 0; i<valores.length; i++) {
		name = valores[i]['name'];
		address = valores[i]['address'];	
		
		latlng = new google.maps.LatLng(
			valores[i]['latitude'],
			valores[i]['longitude']
        );

		//Ifs de pesquisa
		if(pesquisar_nome!=""){	
			var regexp = new RegExp(pesquisar_nome, "i");
			if(regexp.test(name)==false) continue;				
		}
		else if(aluguel!=valor_default){
			if(parseInt(valores[i]['price'])>document.getElementById("aluguel").value) continue;
		}
		else if(tipo!=valor_default){
			if(valores[i]['gener']!=document.getElementById("tipo").value) continue;
		}
		/*else if(deve_rodar==true){
			distance = google.maps.geometry.spherical.computeDistanceBetween(
			    latlng_pesquisado, latlng
			);
			
            distance = distance/1000;
			if(distance>distancia_prox) continue;
		}*/
		else if(document.getElementById("distancia_prox").value!=valor_default){
			if(!deve_rodar){
				if(parseInt(valores[i]['distance'])>document.getElementById("distancia_prox").value) continue;
			}
			else {
				var d = google.maps.geometry.spherical.computeDistanceBetween(
						latlng_pesquisado, latlng
					);
				var d_pesquisada = (document.getElementById("distancia_prox").value) * 1000;
				if(d>d_pesquisada) continue;
			}
		}
		//Fim dos ifs de pesquisa

        try {
			if(markers[i].getMap()!=null) continue;
		} catch(erro){}

		markers[i] =new google.maps.Marker({
			position:latlng,
			map:map,
			title:name,
			icon:"img/mapa-marcador-50-sombra.png" 
		});

        markers[i].setMap(map);	
		encontradas=encontradas+1;
		distance_ate_each = parseInt(google.maps.geometry.spherical.computeDistanceBetween(
			    latlng, centro
			)) / 1000;
		distance_ate_each = distance_ate_each.toFixed(2);
		var sexo;

        /*if(valores[i]['gener']=="masculina") {sexo = "Aceita somente moradores homens";}
		else if (valores[i]['gener']=="feminina"){sexo = "Aceita somente moradoras";}
		else if (valores[i]['gener']=="mista"){sexo = "Aceita moradores de ambos os sexos";}
		else {sexo = "";}*/

        sexo = valores[i]['gener'];

		var conteudo = '<div id="content">' +
			'<h3>'+name+'</h3>' +
			'<p>'+address+'</p>'+
			'<p>'+sexo+' </p>'+
			'<p>Custa R$ '+valores[i]['price']+'</p>'+
			'<p>Distância à EACH (em linha reta): '+distance_ate_each+'km. </p>';//+

        if(document.getElementById("endereco_prox").value!=""){
			var d_pesquisado = Number(valores[i]['distance']).toFixed(2);
			conteudo = conteudo +
			'<p>Distância ao endereço pesquisado (em linha reta): '+d_pesquisado+'km. </p>';
		}	

        conteudo = conteudo +	
			'<p><b>Contato:</b></p>'+
			'<p>Tel: '+valores[i]['phone']+'</p>'+
			'<p>E-mail: '+valores[i]['email']+'</p>'+
			'</div>';

		exibir_nome=name;
		latlng_zoom[i] = markers[i].getPosition();
		setInfoWindow(markers[i], conteudo);
		//adicionar(address, i);
	}
}

function setInfoWindow(marker, conteudo){
	var infowindow = new google.maps.InfoWindow({
		content: conteudo	
	});	
	
    google.maps.event.addListener(marker, 'click', 
	    function() {
			infowindow.setContent(conteudo);
			infowindow.open(map, marker);
		}
    );
}

// FUNCOES DE PESQUISA	
	
function pesquisar(){ //Funcao principal - chama as demais		
	//document.getElementById("loading").style.display="inline";
	//apagaResultados();	
	apagaMarkers(); //Apaga os markers existentes

	var prox = document.getElementById("endereco_prox").value; //Endereço ao redor do qual será feita a pesquisa
	var dist_prox = document.getElementById("distancia_prox").value; //Distancia max ao endereco acima
	var dist_each = document.getElementById("distancia_each").value; //Distancia max até a EACH

	if((prox=="")&&(dist_each==valor_default)) { //Se estao vazios
		marcaRep(valores); 
	}
	else if ((prox=="")&&(dist_each!=valor_default)) { //Se a distancia até a EACH foi delimitada
	    dist_each = dist_each/1000;
		pegaRep(lat_each, lng_each, dist_each);
	}
    else if ((prox!="")&&(dist_each==valor_default)){
		pesquisarAoRedorDe(prox, dist_prox, dist_each);
	}	
	else if((prox!="")&&(dist_each!=valor_default)){
		deve_rodar = true;

		dist_each = dist_each/1000;
		pegaRep(lat_each, lng_each, dist_each);

		//dist_each = dist_each*1000;
		pesquisarAoRedorDe(prox, dist_prox, document.getElementById("distancia_each").value);

		deve_rodar = false;
	}
	
    //document.getElementById('status').innerHTML='<b> realizada! </b>'+encontradas+" repúblicas encontradas.";
	//document.getElementById("loading").style.display="none";
}
	
function pesquisarAoRedorDe(prox, dist_prox, dist_each){	
    geocoder.geocode( { 'address': prox, 'region': 'br'}, function(results, status) {

	    if (status == google.maps.GeocoderStatus.OK) {
		    latlng_pesquisado = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());

            if(bounds.contains(latlng_pesquisado)==false){
			    alert("Caro usuário, as buscas por enndereço se limitam aos contidos na Região Metropolitana de São Paulo. A pesquisa continuará, utilizando os demais parâmetros.");
			    marcaRep(valores);
			    return;
		    }

            if(dist_each!=valor_default){	//Se tem limite de distancia até a EACH
			    var dist_real = google.maps.geometry.spherical.computeDistanceBetween(latlng_pesquisado, centro);
			    var dist_pesq = parseInt(dist_prox*1000)+ parseInt(dist_each);
			    document.getElementById("outros_valores").innerHTML = "Dist real="+dist_real+" Dist pesq="+dist_pesq+" dist_prox="+(dist_prox*1000)+" dist_each="+dist_each;

			    if(dist_pesq<dist_real) {
				    var valor = dist_real - dist_pesq;

                    if(valor<1000){
					    valor= parseInt(valor)+1;
					    alert("Caro usuário, para que seja possível localizar alguma república, a distância à EACH (ou a distância ao endereço pesquisado) deve ser aumentado em no mínimo "+valor+"m.");
					    apagaMarkers();
				    } else {
					    valor = valor/1000;
					    valor= parseInt(valor)+1;
					    alert("Caro usuário, para que seja possível localizar alguma república, a distância à EACH (ou a distância ao endereço pesquisado) deve ser aumentado em no mínimo "+valor+"km.");
					    apagaMarkers();
				    }	
			    } else {
				    marcaRep(valores);
				    map.panTo(latlng_pesquisado);
				    map.setZoom(14);
				    markerPesquisado = new google.maps.Marker({
				        map: map,
					    position: latlng_pesquisado,
					    animation: google.maps.Animation.DROP,
					    icon:"img/blue-dot.png", 
					    title:prox
				    });	
                }
		    } else {
			    pegaRep(latlng_pesquisado.lat(), latlng_pesquisado.lng(), dist_prox);
			    map.panTo(latlng_pesquisado);
			    map.setZoom(14);

                markerPesquisado = new google.maps.Marker({
			        map: map,
				    position: latlng_pesquisado,
				    animation: google.maps.Animation.DROP,
				    icon:"img/blue-dot.png",
				    title:prox
			    });
		    }

        } else {
            alert("Favor redigitar o endereço.");
        }
    });
}
	
//FUNCOES AUXILIARES	
	
function adicionar(endereco, ind){	
	var nome = document.createElement("p");
	var conteudo_nome = document.createTextNode(exibir_nome);	

	var end = document.createElement("p");
	var conteudo_end = document.createTextNode(endereco);
	//alert(nome);

	nome.appendChild(conteudo_nome);
	end.appendChild(conteudo_end);
	nome.setAttribute("class", "nome_da_rep");
	end.setAttribute("class", "end_da_rep");

	//encontradas=encontradas+1;
	var id_nome = "nome"+ind;
	nome.setAttribute("id", id_nome);
	var id_end = "end"+ind;
	end.setAttribute("id", id_end);

	//var pos = markers[ind].getPosition();
	var chamada = "'centralizar("+ind+")'";
	//i_z = ind;
	end.setAttribute("onclick", "centralizar("+ind+")");
	end.style.cursor="pointer";

	document.getElementById("teste").appendChild(nome);
	document.getElementById("teste").appendChild(end);
}
	
function apagaMarkers(){	
	for (var i=0;i<markers.length; i++){
		markers[i].setMap(null);
	}
	
    try {
		markerPesquisado.setMap(null);	
	} catch (erro){
    }
}

function apagaResultados(){
	var velho_div = document.getElementById("teste");
	var novo_div = document.createElement("div");

	document.body.removeChild(velho_div);
	novo_div.setAttribute("id", "teste");

	document.body.appendChild(novo_div);
	//novo_div.style.float = "left";
}

function centralizar(i){
	document.getElementById("status").innerHTML = i;
	//var lala = new google.maps.LatLng(lat_each, lng_each);
	map.panTo(latlng_zoom[i]);
}

function limparCampos(){
	document.getElementById("endereco_prox").value="";
	document.getElementById("distancia_prox").value=valor_default;
	document.getElementById("aluguel").value=valor_default;
	document.getElementById("tipo").value=valor_default;
	document.getElementById("distancia_each").value=valor_default;
	document.getElementById("pesquisar_nome").value="";
}

function habilitarOuDesabilitarDist(){

    if(document.getElementById("endereco_prox").value!="") {
		document.getElementById("distancia_prox").disabled = false;
		document.getElementById("distancia_prox").title = "Distância em linha reta ao endereço pesquisado."
	}
	else {
		document.getElementById("distancia_prox").disabled = true;
		document.getElementById("distancia_prox").value = valor_default;
		document.getElementById("distancia_prox").title = "Se deseja usar este recurso, insira um endereço válido em sua pesquisa."
	}
}

function resetarMapa(){
	//document.getElementById("loading").style.display="inline";
	apagaMarkers();
	//apagaResultados();

	pegaRep(lat_each, lng_each, valor_default);
	limparCampos();
	map.panTo(centro);
	map.setZoom(14);
	//document.getElementById("loading").style.display="none";
}

