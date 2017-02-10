var geocoder = null;
var map = null;
var infowindow = null;
var url = top.url;

//@todo arrumar isso. - eh utilizado em algum lugar
var MarkerColor = new Array('blue', 'brown', 'darkgreen', 'green', 'orange', 'paleblue', 'pink', 'purple', 'red', 'yellow');
var MarkerLetter = new Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
var MarkerIcon = new Array();

z = 0;

for (i = 0; i < 10; i++)
{
    for (j = 0; j < 26; j++)
    {
        MarkerIcon[z] = MarkerColor[i]+'_Marker'+MarkerLetter[j];

        z++;
    }
}

$(document).ready(function() {
    iniMapa();

    $('#pesquisa').click(function(){
        codeAddress();
    });
});



function iniMapa ()
{
    geocoder = new google.maps.Geocoder();
    infowindow = new google.maps.InfoWindow();
    var centerCoord = new google.maps.LatLng(-15.79,-47.88);
    var mapOptions = {
        zoom: 9,
        center: centerCoord,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: true
    };

    map = new google.maps.Map(document.getElementById("mapa"), mapOptions);
}

function reIniMapa ()
{
    var mapOptions = {
        zoom: map.getZoom(),
        center: map.getCenter(),
        mapTypeId: map.getMapTypeId(),
        scrollwheel: true
    };

    map = new google.maps.Map(document.getElementById("mapa"), mapOptions);
}

function codeAddress() {
    var address = document.getElementById("address").value;
    if (geocoder) {
        geocoder.geocode( {'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                map.setCenter(results[0].geometry.location);
                var marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });
            } else {
                // pegar todos os tipos de erros e colocar num switch para mostrar na tela.
                alert("Geocode was not successful for the following reason: " + status);
            }
        });
    }
}

(function () {
    google.maps.Map.prototype.markers = new Array();

    google.maps.Map.prototype.addMarker = function(marker) {
        this.markers[this.markers.length] = marker;
    };

    google.maps.Map.prototype.getMarkers = function() {
        return this.markers
    };

    google.maps.Map.prototype.clearMarkers = function() {

        google.maps.event.clearListeners(map);

        if (infowindow) {
            infowindow.close();
        }

        for(var i=0; i<this.markers.length; i++){
            this.markers[i].setMap(null);
        }
    };

    google.maps.Map.prototype.replota = function() {
        for(var i=0; i<this.markers.length; i++){
            if (this.markers[i].getMap() != null) {
                this.markers[i].setMap(map);
            }
//            console.log(this.markers[i], this.markers[i].getMap());
        }
    };

    google.maps.Map.prototype.attachText = function(marker, text) {
        google.maps.event.addListener(marker, 'click', function() {
            infowindow.setContent(text);
            infowindow.open(map, marker);
        });
    };

})();

function limpaMapa() {
    map.clearMarkers();
}

function exibirAreaTempo(url, dt_area, coords) {

    var pontos = coords.split(",");
    var coordenadas = [];

    if (pontos.length < 3) {
        $('#alerta_msg').html('Zona incorreta, marque ao menos 3 pontos.');
        $('#alerta').dialog('open');
    } else {
        $.getJSON(url, {dt_area: dt_area, coords: coords}, function(data)
        {
            var info;

            if (data.total == 0) {
                $('#alerta_msg').html('N&atilde;o foram encontrados usu&aacute;rios na sua pesquisa.');
                $('#alerta').dialog('open');
            } else {
                $.each(data.pontos, function(i, item)
                {
                    var icone = markerIcone(item);

                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng(item.nu_latitude, item.nu_longitude),
                        map: map,
                        Title: 'Titulo do balao do dispositivo',
                        icon: icone
                    });

                    info = infoHtml(item);

                    map.attachText(marker, info);
                    map.addMarker(marker);
                });
            }
        });
    }

    for (var $i = 0; $i < Math.ceil(pontos.length/2); $i++)
    {
        $index0 = ($i*2);
        $index1 = ($i*2)+1;
        coordenadas.push(new google.maps.LatLng(pontos[$index0], pontos[$index1]));
    }

    var poligono = new google.maps.Polygon({
        paths: coordenadas,
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 3,
        fillColor: "#FF0000",
        fillOpacity: 0.35
    });

    poligono.setMap(map);
    map.addMarker(poligono);
}

function rastreamentoPeriodo (obj, exportar)
{
    if (!exportar) exportar = 0;

    var filtrar = 0;

    if (obj[5].checked == true)
    {
        filtrar = 1;
    }

    if (exportar == 0)
    {
        if (obj[4].checked == true)
        {
            limpaMapa();
        }

        exibirRastreamento(obj[1].value, obj[2].value, obj[3].value, filtrar, obj[0].value);
    }
}

function exibirRastreamento (url, id_dispositivo_usuario, dt_inicial, dt_final, filtrar, rastreamento)
{
    $.getJSON(url,
        {id_dispositivo_usuario: id_dispositivo_usuario, dt_inicial: dt_inicial, dt_final: dt_final, filtrar: filtrar, rastreamento: rastreamento},
        function(data)
        {
            var pontos = new Array();
            var j = 0;
            var mostraPonto = 0;
            var info;
            if ((data.total > 800) || (data.pontos > 200)) {
                $('#alerta_msg').html('Muitos pontos foram encontrados na sua pesquisa.');
                $('#alerta').dialog('open');
            } else if (data.total == 0) {
                $('#alerta_msg').html('N&atilde;o foram encontrados pontos na sua pesquisa.');
                $('#alerta').dialog('open');
            } else {

                $.each(data.items, function(i, item)
                {
                    pontos[j] = new google.maps.LatLng(item.nu_latitude, item.nu_longitude);

                    if (item.ds_dispositivo_usuario)
                    {
                        var icone = markerIcone(item);

                        var marker = new google.maps.Marker({
                            position: pontos[j],
                            map: map,
                            Title: 'Titulo do balao do dispositivo',
                            icon: icone
                        });

                        info = infoHtml(item);

                        map.attachText(marker, info);
                        map.addMarker(marker);
                    }
                    j++;
                });

                var polyline = new google.maps.Polyline({
                    path: pontos,
                    strokeColor: '#0000FF',
                    strokeOpacity: 0.6,
                    strokeWeight: 2
                });

                polyline.setMap(map);
                map.addMarker(polyline);
            }
        });
}

//var markerDispositivo;

function localizarUsuario (url, id_dispositivo_usuario)
{
    $.post(url, {id_dispositivo_usuario: id_dispositivo_usuario}, function(data) {
        // sobreescreve o marker do dispositivo
//        if (markerDispositivo) {
//            markerDispositivo.setMap(null);
//        }

        if (!data.nu_latitude) {
            $('#alerta_msg').html('n&atilde;o foi possivel encontrar uma coordenada valida para este usuario.');
            $('#alerta').dialog('open');
        } else {
            var icone = markerIcone(data);

            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(data.nu_latitude, data.nu_longitude),
                map: map,
                Title: 'Titulo do balao do dispositivo',
                icon: icone
            });

            info = infoHtml(data);

            map.attachText(marker, info);
            map.addMarker(marker);

        }
    }, "json");
}


/**
 * funcao para plotar a zona
 */
function localizarZona(url, id_zona)
{
    $.post(url, {id_zona: id_zona},
        function(data) {
            var pontos = data.nu_coordenadas.split(",");
            var coords = [];

            if (pontos.length < 3) {
                $('#alerta_msg').html('Zona sem area cadastrada.');
                $('#alerta').dialog('open');
            } else {
                for (var $i = 0; $i < Math.ceil(pontos.length/2); $i++)
                {
                    $index0 = ($i*2);
                    $index1 = ($i*2)+1;
                    coords.push(new google.maps.LatLng(pontos[$index0], pontos[$index1]));
                }

                var poligono = new google.maps.Polygon({
                    paths: coords,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 3,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35
                });

                poligono.setMap(map);
                map.addMarker(poligono);
            }
        }, "json"
    );
}

var poligono;

function markarPoligono (field, tabIndex)
{
    limpaMapa();
    var color = '#24951b';

    $('#tabs').tabs('select', 0);

    poligono = new google.maps.Polygon({
        strokeColor: color,
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: color,
        fillOpacity: 0.35
    });

    poligono.setMap(map);
    map.addMarker(poligono);

    // Add a listener for the click event
    google.maps.event.addListener(map, 'click', function(event) {
        addPoligonoLatLng(event, tabIndex, field);
    });
}


/**
 * Handles click events on a map, and adds a new point to the Polyline.
 * @param {MouseEvent} mouseEvent
 */
function addPoligonoLatLng(event, field, tabIndex) {
    var path = poligono.getPath();

    // Because path is an MVCArray, we can simply append a new coordinate
    // and it will automatically appear
    path.push(event.latLng);

    // Add a new marker at the new plotted point on the polyline.
    var marker = new google.maps.Marker({
        position: event.latLng,
        title: '#' + path.getLength(),
        map: map
    });

    map.addMarker(marker);

    if (path.getLength() == 1) {
        // Add a listener for the click event
        google.maps.event.addListener(marker, 'click', function () {
            finalizaArea(path, tabIndex, field);
        });
    }
}


function finalizaArea (path, field, tabIndex)
{
    if (path.getLength() < 3) {}
    else if (confirm("Deseja Finalizar a Area?") == true)
    {
        var stringCoordenadas = '';

        path.forEach(function(a,b) {
//            console.log(a, stringCoordenadas);
            if (stringCoordenadas != '') stringCoordenadas += ',';
            stringCoordenadas += a.lat() + ',' + a.lng();
        });

        if (tabIndex != '') $('#tabs').tabs('select', tabIndex);

        field.val(stringCoordenadas);

        limpaMapa();
    }
    else
    {
        if (confirm("Pressione OK para continuar com Area atual\n ou Cancelar para reiniciar") == false)
        {
            limpaMapa();
            markarPoligono(field, tabIndex);
        }
    }
}

function markerIcone (item)
{
    if (typeof(top.getImage) == 'function') {
        var icon = top.getIcone(item);
    } else {
        // 1 - Sem ocorrencia, 2 - Com ocorrencia
        var ocorrencia = 0;
        if (item.ocorrencia) ocorrencia = 1 + parseInt(item.ocorrencia);

        var origin = new google.maps.Point('0', '40');

        // @todo icone dinamico
        var icon = new google.maps.MarkerImage('marker.php?'+item.id_dispositivo_usuario+'|'+ocorrencia, 0, 0, origin);
    }

    return icon;
}


function infoHtml (item)
{
    if (typeof(top.getHtml) == 'function') {
        html = top.getHtml(item);
    } else {
        if (item.nu_velocidade < 10) item.nu_velocidade = '<' + item.nu_velocidade;
        if (item.nu_bat)
        {
            var bateria = 0;

            if (item.nu_bat > 4100) bateria = 9;
            else if (item.nu_bat > 4000) bateria = 8;
            else if (item.nu_bat > 3950) bateria = 7;
            else if (item.nu_bat > 3900) bateria = 6;
            else if (item.nu_bat > 3820) bateria = 5;
            else if (item.nu_bat > 3780) bateria = 4;
            else if (item.nu_bat > 3740) bateria = 3;
            else if (item.nu_bat > 3700) bateria = 2;
            else if (item.nu_bat > 3680) bateria = 1;
        }

        var html = '';
        html = '<table border="0" cellpadding="4" cellspacing="0" style="font:11px Verdana;line-height:1.2em;">';

        html += '<tr><td rowspan="2"><img src="img/nophoto.gif" style="width:55px;height:55px;"></td><td colspan="2">Nome<br/><span style="color:#aaa;font-size:11px;">'+item.ds_dispositivo_usuario+' ('+item.id_dispositivo_usuario+')</span> <img src="battery.php?' + bateria + '" title="Bateria: ' + item.nu_bat + '" style="margin-left:10px;"></td></tr>';
        html += '<tr><td>Comunica&ccedil;&atilde;o<br/><span style="color:#aaa;font-size:9px;">' + item.dt_pacote + '</span></td><td>Localiza&ccedil;&atilde;o<br/><span style="color:#aaa;font-size:9px;">' + item.dt_ponto + '</span></td></tr>';
        html += '<tr><td><img src="signal.php?4"></td><td>Velocidade<br/><span style="color:#aaa;font-size:11px;">' + item.nu_velocidade + ' km/h</span></td><td>Temperatura<br/><span style="color:#aaa;font-size:11px;">' + item.nu_temperatura+ '&ordm;</span></td></tr>';
        html += '</table>';
        /*
         var html = '<p style="font-size: 14px;">';
         if (item.ds_dispositivo_usuario) html += '<b>Dados</b>';
         if (item.ds_dispositivo_usuario) html += ' <br>Nome: '+item.ds_dispositivo_usuario;
         if (item.nu_bat) html += ' <br>Bateria: '+;
         if (item.dt_pacote) html += ' <br>Comunica&ccedil;&atilde;o: ;
         if (item.dt_ponto) html += ' <br>Localiza&ccedil;&atilde;o: '+item.dt_ponto;
         if (item.nu_temperatura) html += ' <br>Temperatura: '+item.nu_temperatura + '&ordm;';

         if (item.nu_velocidade <= 10) item.nu_velocidade = '< 10';
         html += "<br>Velocidade: "+item.nu_velocidade+" Km/h";


         html += "<br> </p>";
         */
    }

    return html;
}

var markers = [];

function rastreamentoContinuado(url)
{
    var position;
    var path;
    var poly;
    var info;
    var dispositivos = [];
    var icone;

    var polyOptions = {
        strokeColor: '#000000',
        strokeOpacity: 0.7,
        strokeWeight: 2
    };

    if (infowindow) {
        infowindow.close();
    }

//    console.log(markers);

    $.getJSON(url, function(data)
    {
//        console.log(data);
        $.each(data, function(i, item)
        {
            icone = markerIcone(item);
//            console.log('each data');
            info = infoHtml(item);
            position = new google.maps.LatLng(item.nu_latitude, item.nu_longitude);

            if (markers[item.id_dispositivo_usuario]) {
//                console.log('muda position');
                // dar setPosition e novo infowindow = fechou
                marker = markers[item.id_dispositivo_usuario];

//                console.log(marker);

                // adicionar linha -> posicao antiga (marker.getPosition()) para atual(position)
                if ((item.st_rastrear == 1) && (marker.getMap() != null)) {
                    poly = new google.maps.Polyline(polyOptions);
                    poly.setMap(map);

                    path = poly.getPath();
                    path.push(marker.getPosition());
                    path.push(position);

                    map.addMarker(poly);
                }

                marker.setMap(map);
                marker.setPosition(position);
            } else {
//                console.log('novo');

                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(item.nu_latitude, item.nu_longitude),
                    map: map,
                    Title: 'Titulo do balao do dispositivo',
                    icon: icone
                });

                marker.setMap(map);
                markers[item.id_dispositivo_usuario] = marker;
                map.addMarker(marker);
            }

            map.attachText(marker, info);
            dispositivos[item.id_dispositivo_usuario] = true;
        });

//        console.log('2nd parte');
//        console.log(dispositivos);
        // retira todos os sobrando.
        for (var i in markers)
        {
//            console.log('dentro do each de baixo'+ dispositivos[i] +' ------ ' + i + '--'+ markers[i]);
            if (dispositivos[i] != true) {
                markers[i].setMap(null);
            }
        }
    });
}


var ponto;

function escolherPonto (title, field)
{
    // Add a listener for the click event
    google.maps.event.addListener(map, 'click', function(event) {
        addLatLng(event, title, field);
    });
}


function escolherLatLng(event, title, field)
{
    if (ponto.getMap() != null) {
        ponto.setMap(null);
    }

    // Add a new marker at the new plotted point.
    var marker = new google.maps.Marker({
        position: event.latLng,
        map: map
    });

    map.addMarker(marker);

    // Add a listener for the click event
    google.maps.event.addListener(marker, 'click', function () {
        if (confirm("Confirma que esta coordenada inicial?") == true)
        {
            if (title != '') parent.dhxWins.window(title).park();
            field.value = event.latLng;
            limpaMapa();
        }
    });
}
