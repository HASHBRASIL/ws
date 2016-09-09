$(document).ready(function() {

    PNotify.prototype.options.styling = "bootstrap3";


    $(window).resize(resized);
    $(".chosen-select").chosen({});
    CKEDITOR.config.skin = 'minimalist';

    function resized(event){

        var min_height = $('#profile').position().top + $('#profile').height();
        var container  = $('#boxes-container > div.container-fluid');

        if( container.height() <=  $(window).height() ){
            var height = 0;
            if($(window).height() < min_height )
                height = min_height;
            else
                height = $(window).height();

            $('#boxes-container').height( height );
            $('#col-01').height( height );

        }else{
            $('#col-01').height( container.height() + 135 );
        }
    }

    $("#boxes-container").watch('height', function(){
        resized();
    }, 100, 'watchHeight');

    if($('#profile').length){
        resized();
    }

    $('#super-search-bar').chosen({width: '100%'});

    $("#selectEntity").change(function(){
        callAjax({id_grupo: $(this).children('option:selected').val()}, '#listMenu', 'includes/listMenu.php');
    });

    $("#selectEntitytopo").change(function(){
        callAjax({id_grupo: $(this).children('option:selected').val()}, '#listMenutopo', 'includes/listMenu.php');
    });

    $("#selectEntity").chosen({width: "100%"});
    $("#selectEntitytopo").chosen({width: "500px"});

    $("#formUpdate button[type=submit]").click(function(e){

        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $("#formUpdate").submit(function(e){

        var val = $("button[type=submit][clicked=true]").attr('id');
        if(val == 'duplicateMaster'){
            $("#formUpdate").attr('action', 'includes/duplicateDataMaster.php');
        }

        var data = $("#formUpdate").serialize();

        $.ajax({
            url: $("#formUpdate").attr('action'),
            type: 'POST',
            data: data,
        }).done(function(response) {
            if(response == 'error'){
                alert('Erro ao salvar!');
            }else{
                alert('Sucesso!');
            }
        });
        e.preventDefault();
    });

    $('.formaction').bind('click', function(e){
        var form          = $(this).closest('form');
        var servico       = $(this).attr('data-servico');
        var show          = $(this).attr('data-show');

        if (typeof trataForm == "function") {
            var data = trataForm(form);
        } else {
            var data = $(form).serializeArray();
        }

        if(show != 'reload') {
            e.preventDefault();
            var ret = ajaxFunction(servico, data, show);


        } else {
            $(form).find("input[name*='servico']").val(servico);
        }
    });


    $('.action').bind('click', function(e){
        var servico       = $(this).attr('data-servico');
        var show          = $(this).attr('data-show');

        switch(show) {
            case 'ajax':
                // break omitido intencionalmente
            case 'modal':
                var ret = ajaxFunction(servico, null, show);
                break;
            case 'reload':
                window.location.href = "home.php?servico=" + servico;
                break;
            default:
                break;
        }

    });

    $(window).scroll(function () {
        $.each($('.paginacao'), function(i,v) {
            //var servico       = $(this).attr('data-servico');
            if ($(window).scrollTop() + $(window).height() == $(document).height()) {
                totalItens = $('table.table > tbody > tr').length;
                ajaxFunction($(this).attr('data-servico'), {total: totalItens}, 'paginacao');
            }
        });
    });

    $(".paginacao").delegate(".listaction", "click", function(e){
        var servico       = $(this).attr('data-servico');
        var show          = $(this).attr('data-show');
        var confirm          = $(this).attr('data-confirm');

        switch(show) {
            case 'ajax':
            // break omitido intencionalmente
            case 'modal':

                var data = {'id' : $(this).closest('tr').attr('data-id')};
                if (confirm) {
                    bootbox.confirm(confirm, function(result) {
                        if (result) {
                            var ret = ajaxFunction(servico, data, show);
                        }
                    });
                } else {
                    var ret = ajaxFunction(servico, data, show);
                }

                if (ret.data.itemremove == 1) {
                    $(this).closest('tr').remove();
                }

                break;
            case 'reload':
                window.location.href = "home.php?servico=" + servico + "&id=" + $(this).closest('tr').attr('data-id');
                break;
            default:
                break;
        }

    });


    $.ajaxSetup({
        //timeout: 30000, // definir o timeout default do sistema.
        error: function (response, status, XMLHttpRequest) {

            msg_error(response.getResponseHeader('Error-Message'));

            $("#btnSubmit").text('Login').removeAttr('disabled');
        }
    });

});

function processAjaxSuccess(response, status, xhr)
{
    if (typeof response == 'string') {
        response = $.parseJSON(response);
    }

    if (response.msg) {
        if (response.error) {
            msg_error(response.msg);
        } else {
            msg_success(response.msg);
        }
    }

    if (response.error) {
        return false;
    }

    return true;
}

function callAjax(data, container, url){
    $.ajax({
        method: "POST",
        url: url,
        data: data
    }).done(function(rtn){
        $(container).html(rtn);
    });
}

function ajaxFunction(servico, data, show){
    $.ajax({
        type: "POST",
        url: 'home.php?servico=' + servico,
        data: data,
        success: function(response, status, XMLHttpRequest) {

            if (typeof response == 'string') {
                response = $.parseJSON(response);
            }

            if (!processAjaxSuccess(response, status, XMLHttpRequest)) {
                return;
            }

            switch(show){
                case 'paginacao':
                    loadTable(response.data);
                    break;
                case 'ajax':
                    //MOSTRAR msg de sucesso / erro

                    if (response.data.target.servico) {
                        var data = $.param(response.data.target);

                        window.location.href = "home.php?" + data;
                    }
                    return response;
                    break;
                case 'modal':

                    //CHAMA MODAL E INSERE HTML NO MODAL
                    bootbox.dialog({
                        title: response.data.title,
                        message: response.data.html
                    });

                    break;
                case 'reload':
                    // aqui nunca entra.

                    break;
                default:
                    break;
            }
        },
        //error: function(result) {
        //
        //}
    });
}

function GetQueryStringParams(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    }
}

function mostraMSG(idMensagem, idTexto, sucesso)
{

    // Ao clicar na mensagem retornada, esconder.
    $( idMensagem ).click(function() {
        $( idMensagem ).fadeOut( "slow" );
    });


    switch( idTexto ) {
        case 1:
            texto = "Dados salvos com sucesso.";
            break;
        case 2:
            texto = "Dados atualizados com sucesso.";
            break;
        case 3:
            texto = "Dados duplicados com sucesso.";
            break;
        case 4:
            texto = "Ocorreu algum erro, entre em contato com o suporte.";
            break;
        default:
            texto = idTexto;
    }

    if(sucesso == true){
        // Mensagem de sucesso
        $( idMensagem ).removeClass('hidden').show();
        $( idMensagem ).text( texto );

    }else{
        // Mensagem de erro
        $( idMensagem ).removeClass("hidden").removeClass("alert-success").addClass("alert-danger").show();
        $( idMensagem ).text( texto );
    }
}

/**
*   form:           Formulário que esta sendo trabalhado
*   formSerialize:  Formulário serializado
*   formData:       Objeto FormData()
**/
function getCKEditorData( form, formSerialize, formData )
{
    // Percorre todos os textareas do form
    $.each( $(form).find( "textarea" ), function ( i, v ) {
        // atribui a variavel editor a instancia do ckeditor pega pelo id do textarea
        // onde v é todo o objeto textarea
        editor = CKEDITOR.instances[$( v ).attr( 'name' )].getData();
        //console.log( editor );
        // percorre todo o array arData
        $.each( formSerialize, function ( indice, value ) {
            // e pergunta se o name do arData é o mesmo do textarea
            if ( value['name'] == $( v ).attr( 'name' ) ) {
                // caso seja, no indice que bateu de o name do arData e o name do textarea são iguas
                // ele atribui o valor retornado pela variavel editor.
                if (formData) {
                    formData.append( value['name'], editor );
                }else{
                    formSerialize[indice]['value'] = editor;
                }
            }
        });
    });
}


function loadTable(data)
{
    var tr = $('table.paginacao tbody tr:first');

    $.each(data, function(i, row) {
        var clone = tr.clone();
        $.each(clone.find('td'), function(k,v) {
            if ($(v).data('field')) {
                $(v).html(row[$(v).data('field')]);
            }
        });
        $(clone).removeAttr('style');
        $(clone).attr('data-id', row.id);

        $('table.paginacao > tbody').append(clone);
    });

}


function msg (message, type, title)
{
    title = title || "Aviso!";

    new PNotify({
        title: title,
        text: message,
        hide: false,
        type: type,
        buttons: {
            sticker: false,
            closer_hover: false
        }
    });
}

function msg_error(message)
{
    msg(message, 'error');
}

function msg_success(message)
{
    msg(message, 'success');
}

function msg_warning(message)
{
    msg(message);
}

function msg_info(message)
{
    msg(message, 'info');
}
