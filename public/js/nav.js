$(document).ready(function()
{
    var blocos = {};
    var tamTela = screen.availHeight - 30;
    var larguraColuna = 250;

    var menuHeightMax = tamTela - (tamTela/4);

    $('.btn-show-times').click(function(e){
        e.preventDefault();

        $('.lista-grupos').removeClass('opened').addClass('closed');
        $('.area-nav-menu .lista-modulos').removeClass('opened').addClass('closed');

        var container = $('.lista-times');
        if (container.hasClass('opened')) {
            container.removeClass('opened').addClass('closed');
        } else {
            container.removeClass('closed').addClass('opened');
        }
    });

    $('.btn-show-grupos').click(function(e){
        e.preventDefault();

        $('.lista-times').removeClass('opened').addClass('closed');
        $('.area-nav-menu .lista-modulos').removeClass('opened').addClass('closed');

        var container = $('.lista-grupos');
        if (container.hasClass('opened')) {
            container.removeClass('opened').addClass('closed');
        } else {
            container.removeClass('closed').addClass('opened');
        }
    });

    $( ".grupo" ).on( "click", function(e) {
        e.preventDefault();
        var id = $(this).attr("id");
        changeGrupo(id, name);

    });

    $( ".time" ).on( "click", function(e) {
        e.preventDefault();
        var id = $(this).attr("id");
        changeTime(id, name);

    });
// return false;
    $('.menu-servicos-modulo ul li >.submenu').css('max-height', menuHeightMax+'px');

    $('.submenu').show(); $('.sub-menu-hash').show();

    var i = 0;
    $('.menu-servicos-modulo ul li .submenu').each(function(){
        blocos[i] = {pai : $(this).parent(), filhos : []};

        $(this).children('ul').children('li').each(function(){
            if($(this).hasClass('has-sub') === false){
                blocos[i].filhos.push({ 'height' : $(this).outerHeight(), 'html' : $(this).html()+'<br>'});
            }else{
                blocos[i].filhos.push({ 'height' : $(this).outerHeight(), 'html' : $(this).html()});
            }
        });
        ++i;
    });

    $('.menu-servicos-modulo .submenu').remove();

    for(var b in blocos)
    {
        blocos[b].filhos.sort(function(a,b) { return a.height - b.height; });

        var colums = {0:[]};
        var c = 0;
        var soma = 0;

        for(var i = 0; i < blocos[b].filhos.length; ++i)
        {
            if((soma + blocos[b].filhos[i].height) >  menuHeightMax){
                ++c;
                soma = 0;
                colums[c] = [];
            }
            soma += blocos[b].filhos[i].height;
            colums[c].push(blocos[b].filhos[i].html);
        }

        var retorno = '<div class="submenu" asd="3" style="width:'+(larguraColuna*(c + 1))+'px">';

        retorno += '<header>';
        retorno += blocos[b].pai.children('a').html();
        retorno += '</header>';

        for(var c in colums)
        {
            retorno += '<div>';
            for(var i = 0; i < colums[c].length; ++i)
            {
                retorno += colums[c][i];
            }
            retorno += '</div>';
        }

        retorno += '</div>';

        blocos[b].pai.append(retorno);
    }

    $('.menu-servicos-modulo .submenu header .fa-angle-right').remove();

    $('.menu-servicos-modulo >ul >li').mouseleave(function(e){
        $('.submenu').hide();
    });

    $('.menu-servicos-modulo >ul >li').mouseenter(function(e){
        $(this).children('.submenu').show();

        e.preventDefault();

        var filho = $(this).children('.submenu');
        var filhoHeigth = filho.outerHeight();
        var eventPositionY = e.clientY;
        var screenHeightBottom  = screen.availHeight + $(document).scrollTop();

        if(screenHeightBottom < filhoHeigth + eventPositionY){

            var posYFinal = filhoHeigth/2;
            if(posYFinal > (eventPositionY/2)){
                posYFinal = (eventPositionY/2);
            }

            filho.css('top', '-'+posYFinal+'px');
        }
    });
});

function changeModule(id) {
    $.ajax({
        type: "POST",
        url: 'auth/grupo/change-module',
        data: { id: id },
        success: function(data){
            if(data.success == true) {
                window.location.href = 'default';
            } else {
                alert("Não foi possivel selecionar o Modulo.");
            }
        }
    });

}

function changeGrupo(id){
    $.ajax({
        type: "POST",
        url: 'auth/grupo/change-grupo',
        data: { id: id},
        success: function(data){
            if(data.success == true) {
                window.location.reload();
            } else {
                alert("Não foi possivel selecionar o Workspace.");
            }
        }
    });
}

function changeTime(id){
    
    if($('.lista-times .carregando-time').length > 0){ return false; }
    $('#'+id).attr("disabled", 'disabled');
    $('#'+id).append('<i class="carregando-time fa fa-spinner fa-pulse fa-1x fa-fw"></i>');
    
    $.ajax({
        type: "POST",
        url: 'auth/grupo/change-time',
        data: { id: id},
        error : function(){
            alert('Erro ao carregar página');
            $('#'+id).removeAttr("disabled");
            $('#'+id+' .fa').fadeOut().stop().remove();
        },
        success: function(data){
            if(data.success == true) {
                window.location.href = 'default';
            } else {
                alert("Não foi possivel selecionar o Workspace.");
            }
        }
    });
}
