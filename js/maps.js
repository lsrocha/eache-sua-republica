/**
 * Busca e insercao de dados no mapa
 *
 * Google Maps API
 *
 * Author: Renan Souza de Freitas
 */

var map;
var markers = new Array();
var infowindows;
var encontradas;

//Ferramentas do Mapa
var geocoder;
	//Rota
var directionsService;
var poly = new Array();
var veiculo;
var stepDisplay;
var markersRota = new Array();
var rota_txt;
var bounds_rota;
var velho_conteudo;

var markerPesquisado;

var valores;//recebe json convertido

//Variaveis para valores default
var each = new google.maps.LatLng(-23.485565, -46.502963);//EACH no mapa
var valor_default = 100000;
var dupla_distancia = false;
var latlng_pesquisado = new google.maps.LatLng(each.lat(), each.lng());

var exibir_nome;//Se nao for criado (passado como parametro), dá objectParagraphElement
var latlng_zoom = new Array();//Tbm se nao for criado, não funciona


//Limites de pesquisa
var latlng_sw = new google.maps.LatLng(-23.917853,-47.067604); //Juquitiba = Limite inferior da RMSP
var latlng_ne = new google.maps.LatLng(-23.325863,-45.912094); //Ponto em Jacarei = Limite superior da RMSP
var bounds    = new google.maps.LatLngBounds(latlng_sw, latlng_ne);
/*************************************************************************************************************************************************************/
//INICIALIZACAO DO MAPA

function carregar(){
   inicializar();
   pegaRep(each.lat(), each.lng(), valor_default); //Carrega a partir da EACH, todas as reps existentes
}

function inicializar() {
    var prop_map = {
		center:each,
		zoom:14,
		mapTypeId:google.maps.MapTypeId.ROADMAP
	};

    map = new google.maps.Map(document.getElementById('google_map'),prop_map);
	
	//Ferramentas do Mapa
	geocoder = new google.maps.Geocoder();
	directionsService= new google.maps.DirectionsService();
	infowindows = new google.maps.InfoWindow({maxWidth: 250})
	stepDisplay = new google.maps.InfoWindow({
		zIndex: 1000,
		maxWidth: 250
	});
	
	var marker_each =new google.maps.Marker({
			position:each,
			map:map,
			title:"Escola de Artes, Ciências e Humanidades",
			icon:"img/each-marcador.png" 
		});
}
google.maps.event.addDomListener(window, 'load', inicializar);

function pegaRep(latitude, longitude, raio){ //Essa funcao nao eh chamada apenas na inicializacao/ Seu raio eh em km
	var xmlhttp;
	var result;
	var url = "republicas.php?list=1&lat="+latitude+"&lng="+longitude+"&r="+raio+"&n=50";//MUDAR DEPOIS!!!!*****
	
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
				//document.getElementById("print").innerHTML = xmlhttp.responseText;
				valores = eval("("+ xmlhttp.responseText +")");
			    marcaRep(valores);
		    }
		
            if (xmlhttp.status==404){
			    document.write("Not Found");
		    }
	    }
	}
}
/*************************************************************************************************************************************************************/

//FUNCOES DE MANIPULACAO DE MARKERS E INFOWINDOWS

function setInfoWindow(marker, conteudo, i){
    google.maps.event.addListener(marker, 'click', function() {
			infowindows.setContent(conteudo);
			infowindows.open(map, marker);
			apagarRota();
	});
	
	google.maps.event.addListener(infowindows, 'closeclick', function(){
				apagarRota()
	});
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

function centralizar(i){
	map.panTo(markers[i].getPosition());
	google.maps.event.trigger(markers[i], 'click');
}

function attachInstructionText(marker, text) {
  google.maps.event.addListener(marker, 'click', function() {
    stepDisplay.setContent(text);
    stepDisplay.open(map, marker);
  });
}


/*************************************************************************************************************************************************************/

//FUNCOES DE MARCACAO DE DADOS NO MAPA

function marcaRep(valores){
	//apagaResultados();  DESCOMENTAR ao inserir no index.php o visualizador de resultados
	var name, address, latlng, distance, distance_ate_each;
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
		if(pesquisar_nome!=""){	//Filtra por nome da rep
			var regexp = new RegExp(pesquisar_nome, "i");
			if(regexp.test(name)==false) continue;				
		}
		else if(aluguel!=valor_default){//Filtra pelo aluguel
			if(parseInt(valores[i]['price'])>aluguel) continue;
		}
		else if(tipo!=valor_default){//Filtra pelo tipo
			if(valores[i]['gener']!=tipo) continue;
		}
		else if(distancia_prox!=valor_default){//
			if(!dupla_distancia){
				if(parseInt(valores[i]['distance'])>distancia_prox) continue;
			}
			else {
				var d = google.maps.geometry.spherical.computeDistanceBetween(//Calcula a distância entre o ponto pesquisado e a rep
						latlng_pesquisado, latlng
					);
				var d_pesquisada = (document.getElementById("distancia_prox").value) * 1000; //Passa pra metros 
				if(d>d_pesquisada) continue;
			}
		}
		//Fim dos ifs de pesquisa

        try {
			if(markers[i].getMap()!=null) continue; //Se o marker para esta rep já esta no mapa, continua
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
			    latlng, each
			)) / 1000;
		distance_ate_each = distance_ate_each.toFixed(2);

		var sexo;
        if(valores[i]['gener']=="Masculino") {sexo = "Aceita somente moradores homens";}
		else if (valores[i]['gener']=="Feminina"){sexo = "Aceita somente moradoras";}
		else if (valores[i]['gener']=="Mista"){sexo = "Aceita moradores de ambos os sexos";}
		else {sexo = "";}
		
		var price;
		if((valores[i]['price']=="")||(valores[i]['price']==0)) price = "não informado";
		else price = "R$"+valores[i]['price'];

		var conteudo = '<div id="content">' +
			'<h3>'+name+'</h3>' +
			'<p>'+address+'</p>'+
			'<p>'+sexo+' </p>'+
			'<p>Custo mensal: '+price+'</p>'+
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
			"<span onclick='rota("+ valores[i]['latitude']+", "+ valores[i]['longitude']+", "+i+")' style='color:blue;cursor:pointer'><u>Trajeto à EACH </u></span>"+
			'</div>';

		exibir_nome=name;
		latlng_zoom[i] = markers[i].getPosition();
		setInfoWindow(markers[i], conteudo, i);
		//adicionar(address, i); DESCOMENTAR ao inserir no index.php o visualizador de resultados
		
	}
}

// FUNCOES DE PESQUISA	
	
function pesquisar(){ //Funcao principal - chama as demais		
	apagaMarkers(); //Apaga os markers existentes

	var prox = document.getElementById("endereco_prox").value; //Endereço ao redor do qual será feita a pesquisa
	var dist_prox = document.getElementById("distancia_prox").value; //Distancia max ao endereco acima
	var dist_each = document.getElementById("distancia_each").value; //Distancia max até a EACH

	if((prox=="")&&(dist_each==valor_default)) { //Se estao vazios
		marcaRep(valores); 
	}
	else if ((prox=="")&&(dist_each!=valor_default)) { //Se a distancia até a EACH foi delimitada
	    dist_each = dist_each/1000;
		pegaRep(each.lat(), each.lng(), dist_each);
	}
    else if ((prox!="")&&(dist_each==valor_default)){
		pesquisarAoRedorDe(prox, dist_prox, dist_each);
	}	
	else if((prox!="")&&(dist_each!=valor_default)){
		dupla_distancia = true;

		dist_each = dist_each/1000;
		pegaRep(each.lat(), each.lng(), dist_each);

		pesquisarAoRedorDe(prox, dist_prox, document.getElementById("distancia_each").value);

		dupla_distancia = false;
	}
}
	
function pesquisarAoRedorDe(prox, dist_prox, dist_each){	
    geocoder.geocode( { 'address': prox, 'region': 'br'}, function(results, status) {

	    if (status == google.maps.GeocoderStatus.OK) {
		    latlng_pesquisado = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());

            if(bounds.contains(latlng_pesquisado)==false){
			    alert("Caro usuário, as buscas por endereço se limitam aos contidos na Região Metropolitana de São Paulo. A pesquisa continuará, utilizando os demais parâmetros.");
			    marcaRep(valores);
			    return;
		    }

            if(dist_each!=valor_default){	//Se tem limite de distancia até a EACH
			    var dist_real = google.maps.geometry.spherical.computeDistanceBetween(latlng_pesquisado, each);
			    var dist_pesq = parseInt(dist_prox*1000)+ parseInt(dist_each);
			    
			    if(dist_pesq<dist_real) {
				    var valor = dist_real - dist_pesq;

                    if(valor<1000){
					    valor = parseInt(valor)+1;
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
					    title:results[0].formatted_address
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
				    title:results[0].formatted_address
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

	nome.appendChild(conteudo_nome);
	end.appendChild(conteudo_end);
	nome.setAttribute("class", "nome_da_rep");
	end.setAttribute("class", "end_da_rep");

	var id_nome = "nome"+ind;
	nome.setAttribute("id", id_nome);
	var id_end = "end"+ind;
	end.setAttribute("id", id_end);

	//end.setAttribute("onclick", "map.panTo(latlng_zoom["+ind+"])");
	end.setAttribute("onclick", "centralizar("+ind+")");
	end.style.cursor="pointer";

	var pula_linha = document.createElement("p");
	var conteudo_linha = document.createTextNode("___________________________________");
	pula_linha.appendChild(conteudo_linha);
	
	document.getElementById("resultados").appendChild(nome);
	document.getElementById("resultados").appendChild(end);
	document.getElementById("resultados").appendChild(pula_linha);
}

function apagaResultados(){
	var resultados = document.getElementById("resultados");
	for (var i=resultados.childNodes.length-1;i>-1;i--){
		resultados.removeChild(resultados.childNodes[i]);
	}
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


function limparCampos(){
	document.getElementById("endereco_prox").value="";
	document.getElementById("distancia_prox").value=valor_default;
	document.getElementById("aluguel").value=valor_default;
	document.getElementById("tipo").value=valor_default;
	document.getElementById("distancia_each").value=valor_default;
	document.getElementById("pesquisar_nome").value="";
}

function resetarMapa(){
	apagaMarkers();

	pegaRep(each.lat(), each.lng(), valor_default);
	limparCampos();
	map.panTo(each);
	map.setZoom(14);
}

//FUNÇÕES DE TRAJETO

function rota(lat, lng, i){
	
	var origem = new google.maps.LatLng(lat, lng);
	
	velho_conteudo = infowindows.getContent();
	//var velho_conteudo = "Fuck";
	//var cacete = "1c";
	var novo_conteudo = '<div id="content">' +
						'<h3>'+valores[i]['name']+'</h3>'+
						'<h5>Trajeto à EACH</h5>'+
						"<p>Escolha modo de viagem:</p>"+
						'<select id="modo" onchange="">'+
							'<option value="TRANSIT">Transporte público</option>'+
							'<option value="DRIVING">Carro</option>'+
							'<option value="WALKING">Andando</option>'+
							'<option value="BICYCLING">Bike</option></select>'+
						'<p></p>'+
						"<span style='cursor:pointer' onClick='obterRota("+i+")'><b>OK</b></span>"+
						"<p></p>"+
					    '<span id="voltar" onmouseover="sublinhar('+("'voltar'")+')" onmouseout="sublinhar('+("'voltar'")+')" style="color:blue;cursor:pointer" onClick="voltarInfoWindow('+i+')" ><--<u>Voltar</u></span>'+
	"</div>";
	
	infowindows.setContent(novo_conteudo);
	

}

function obterRota(i){
	var origem = new google.maps.LatLng(valores[i]['latitude'], valores[i]['longitude']);
	var modo_viagem = document.getElementById("modo").value;
	
	var request = {
		origin: origem,
		destination: each,
		travelMode: google.maps.TravelMode[modo_viagem]
	};
	
	directionsService.route(request, function(response, status){
		if (status == google.maps.DirectionsStatus.OK) {
	
			bounds_rota = response.routes[0].bounds;
			map.fitBounds(bounds_rota);
			
			var rota = response.routes[0].legs[0];
			
			var a;
			for (a = 0; a < rota.steps.length; a++) {
				markersRota[a] = new google.maps.Marker({
					position: rota.steps[a].start_point,
					map: map,
					zIndex: 1000
				});
				
				//laranja = #FF8D0B azul = #2991A4

				if(modo_viagem!="TRANSIT"){
					poly[a] = new google.maps.Polyline({
						strokeColor: '#2991A4',
						strokeOpacity: 0.9,
						strokeWeight: 5,
						path: rota.steps[a].path
					});
					poly[a].setMap(map);
				} else {
					if (rota.steps[a].travel_mode=="WALKING"){
						poly[a] = new google.maps.Polyline({
							strokeColor: '#FF8D0B',
							strokeOpacity: 0.9,
							strokeWeight: 5,
							path: rota.steps[a].path
						});
						poly[a].setMap(map);
					} else {
						poly[a] = new google.maps.Polyline({
							strokeColor: '#2991A4',
							strokeOpacity: 0.9,
							strokeWeight: 5,
							path: rota.steps[a].path
						});
						poly[a].setMap(map);
					}
				}
				
				var txt = '<p><b>Passo '+(a+1)+':</b> '+rota.steps[a].instructions+'</p>';
				
				if(rota.steps[a].transit!=undefined){
					//alert(rota.steps[a].transit);
					txt = txt +
						//'<p> arrival_stop.name = '+rota.steps[a].transit.arrival_stop.name+'</p>'+
						//'<p> departure_stop.name ='+rota.steps[a].transit.departure_stop.name+'</p>'+
						//'<p> arrival_time.txt = '+rota.steps[a].transit.arrival_time.text+'</p>'+
						//'<p> departure_time.text = '+rota.steps[a].transit.departure_time.text+'</p>'+
						//'<p> headsign ='+rota.steps[a].transit.headsign+'</p>'+
						//'<p> line.name = '+rota.steps[a].transit.line.name+'</p>'+
						//'<p> line.short_name = '+rota.steps[a].transit.line.short_name+'</p>'+
						//'<p> line.agencies.name = '+rota.steps[a].transit.line.agencies.name+'</p>'+
						//'<p> line.agencies.url = '+rota.steps[a].transit.line.agencies.url+'</p>'+
						//'<p> line.url = '+rota.steps[a].transit.line.url+'</p>'+
						//'<p> line.icon = '+rota.steps[a].transit.line.icon+'</p>'+
						//'<p> line.color'+rota.steps[a].transit.line.color+'</p>'+
						//'<p> line.text_color = '+rota.steps[a].transit.line.text_color+'</p>'+
						//'<p> line.vehicle.name = '+rota.steps[a].transit.line.vehicle.name+'</p>'+
						//'<p> line.vehicle.type = '+rota.steps[a].transit.line.vehicle.type+'</p>'+
						//'<p> line.vehicle.icon = '+rota.steps[a].transit.line.vehicle.icon+'</p>'+
						//'<p> line.vehicle.local_icon = '+rota.steps[a].transit.line.vehicle.local_icon+'</p>'+
						'<p></p>'+
						'<p>'+'<img src='+rota.steps[a].transit.line.vehicle.icon+'></img><b> '+rota.steps[a].transit.line.short_name+" - "+rota.steps[a].transit.headsign+'</b></p>'+
						'<p>Linha: '+rota.steps[a].transit.line.name+'</p>'+
						'<p>'+rota.steps[a].transit.departure_time.text+' - Partida: '+rota.steps[a].transit.departure_stop.name+'</p>'+
						'<p>'+rota.steps[a].transit.arrival_time.text+' - Destino: '+rota.steps[a].transit.arrival_stop.name+'</p>';
				}				
				
				/*
				for(var b=0;b<response.routes[0].warnings.length;b++){
					txt  = txt +'<p><small>'+response.routes[0].warnings[b]+'</small></p>';
				}*///WARNINGS!!
				
				if((a-1)>-1){
					txt = txt +
						'<span id="ant'+(a-1)+'" style="color:blue;cursor:pointer" onmouseover="sublinhar('+("'"+"ant"+(a-1)+"'")+')" onmouseout="sublinhar('+("'"+"ant"+(a-1)+"'")+')" onClick="proximoPasso('+(a-1)+')"><--Anterior</span>';
				} else {
					txt = txt + '<span style="color:gray;"><--Anterior</span>'+'<span> </span>';
				}
				
				txt = txt +
						'<span> </span>'+'<span id="rota_zoom" style="color:blue;cursor:pointer" onmouseover="sublinhar('+("'rota_zoom'")+')" onmouseout="sublinhar('+("'rota_zoom'")+')"  onClick="ampliarOuReduzir('+a+')">';
				
				if(map.getBounds()==bounds_rota) txt = txt + 'Ampliar';
				else txt = txt + 'Reduzir';
				
				txt = txt + '</span>'+
						'<span> </span>'+'<span id="ini" style="color:blue;cursor:pointer" onmouseover="sublinhar('+("'ini'")+')" onmouseout="sublinhar('+("'ini'")+')" onClick="centralizar('+i+')">Início</span>'+
						'<span> </span>'+'<span id="prox'+(a+1)+'" style="color:blue;cursor:pointer" onmouseover="sublinhar('+("'"+"prox"+(a+1)+"'")+')" onmouseout="sublinhar('+("'"+"prox"+(a+1)+"'")+')"  onClick="proximoPasso('+(a+1)+')">Próximo--></span>';

				
				attachInstructionText(markersRota[a], txt);
			}
			
			markersRota[a] = new google.maps.Marker({
				position: rota.end_location,
				map: map,
			});
			
			txt = '<h4>Você chegou à EACH!</h5>'+
				'<p><big>Bons estudos!</big></p>'+
				'<p><small>Cervejadas às quartas-feiras.</small></p>'+
				'<span id="fim" style="color:blue;cursor:pointer" onmouseover="sublinhar('+("'fim'")+')" onmouseout="sublinhar('+("'fim'")+')"  onClick="proximoPasso('+(a-1)+')"><--Anterior</span>'+'<span> </span>';
			attachInstructionText(markersRota[a], txt);
				
			var novo_conteudo = '<div id="content">' +
						'<h3>'+valores[i]['name']+'</h3>'+
						'<h5>Trajeto à EACH</h5>'+
						'<p>Distância total: ' + rota.distance.text + '</p>'+
						'<p>Tempo estimado: '+ rota.duration.text+'</p>';
						
			for(var b=0; b<markersRota.length;b++){
				if((b+1)!=markersRota.length)novo_conteudo = novo_conteudo+'<span id="passo'+(b+1)+'"style="color:blue;cursor:pointer" onmouseover="sublinhar('+("'"+"passo"+(b+1)+"'")+')" onmouseout="sublinhar('+("'"+"passo"+(b+1)+"'")+')" onClick="proximoPasso('+b+')">Passo '+(b+1)+'</span></br>'
				else novo_conteudo = novo_conteudo + '<span id="fim" onmouseover="sublinhar('+("'fim'")+')" onmouseout="sublinhar('+("'fim'")+')"style="color:blue;cursor:pointer" onClick="proximoPasso('+b+')"> Fim</span></br>';
			}			
						
			novo_conteudo = novo_conteudo +
						'<p></p>'+
						'<p><small>'+response.routes[0].copyrights+'</small></p>';

			novo_conteudo = novo_conteudo + '<span id="voltar2" onmouseover="sublinhar('+"'voltar2'"+')" onmouseout="sublinhar('+"'voltar2'"+')" style="color:blue;cursor:pointer" onClick="voltarInfoWindow(1)" ><--Voltar</span>'+
						'</div>';
						
			infowindows.setContent(novo_conteudo);
			infowindows.open(map, markers[i]);
		
		}
	});
	
}

function ampliarOuReduzir(a){
	if(document.getElementById("rota_zoom").innerHTML=="Ampliar"){
		map.panTo(markersRota[a].getPosition());
		map.setZoom(16);
		document.getElementById("rota_zoom").innerHTML="Reduzir";
	} else {
		map.fitBounds(bounds_rota);
		document.getElementById("rota_zoom").innerHTML="Ampliar";
	}
}

function proximoPasso(a){
	map.panTo(markersRota[a].getPosition());
	google.maps.event.trigger(markersRota[a], 'click');
}

function apagarRota(){
	stepDisplay.close();
	for(var a = 0; a<markersRota.length; a++){
		markersRota[a].setMap(null);
	}
	for(var a=0; a<poly.length; a++){
		poly[a].setMap(null);
	}
}

function sublinhar(id){
	var a = document.getElementById(id);
	
	if(a.style.textDecoration=="underline") {
		a.style.textDecoration="none";
	}
	else{
		a.style.textDecoration="underline";
	}
}

function voltarInfoWindow(i){
	infowindows.setContent(velho_conteudo);
	apagarRota();
}