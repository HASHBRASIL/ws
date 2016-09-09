$(document).ready(function(){
    FormWizard.init();

    if($('#pro_id').val() != "") {
        //Historico do processo
        $.ajax({
            type: "GET",
            url: "processo/historico/grid/pro_id/" + $('#pro_id').val() + '/limit/5',
            success: function (data) {
                $("#grid-historico").html(data);
            }
        });
    }

    //Mostra o tempo trabalhado
    if($('#pro_id').val() != ""){
        $.ajax({
            type: "GET",
            url: "processo/pcp-timer/grid-processo/id_processo/"+$('#pro_id').val(),
            success: function(data){
                    $("#grid-pcp").html(data);
            }
        });
    }

    //Historico do processo
    $('body').on('click', 'a.limit_historico', function(e){
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: "processo/historico/grid/pro_id/"+$('#pro_id').val(),
            success: function(data){
                    $("#grid-historico").html(data);
            }
        });
    });
    $('.decimal').setMask('decimal');
    $('.int').setMask('integer');
    initEntrega();
    $('#pro_data_entrega').setMask('99/99/9999 99:99');
    //$('#pro_desc_produto').limit('500','#countDescricao');
    //$('#pro_prazo_entrega').limit('255','#countObserv');
    //aparece o calendário no campo data fim
    $("#pro_data_entrega").datetimepicker({
        format: "dd/mm/yyyy hh:ii",
        language: 'pt-BR',
        autoclose: true,
        todayBtn: true,
        pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
        minuteStep: 5,
    });

    //autocomplete da entidade
    $( "#empresa_sacado" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#empresas_id').val(ui.item.id);
        },
        search: function( event, ui ) {
            $('#empresas_id').val("");
        }
      });//endautocomplete

    $('.entrega').change(function(){
        if($('.entrega:checked').val() == 0){
            $('.entrega_parcial:not(:first)').remove();
            $('.entrega_parcial').hide('slow');
            $('.entrega_total').show('slow');
            $('.id_empresa_entrega, .dt_entrega, .quantidade_entrega, .empresa_entrega').val('');
        }else{
            $('.entrega_parcial').show('slow');
            $('.entrega_total').hide('slow');
            $('#pro_data_entrega, #pro_prazo_entrega').val('');

        }
    });

    $('body').on('focusout', '.quantidade_entrega', function(){
        var total = 0;
        $('.quantidade_entrega').each(function(index){
            if($(this).val() != ""){
                total += parseInt($(this).val().replace(/\./g, ''));
            }
        });
        var quantidade_produzir = $('#pro_quantidade').val().replace(/\./g, '');
        if(total > quantidade_produzir){
            alertPedido('O total de entrega e maior que o total a produzir.');
        }
    });

    //adicionar uma entrega parcial
    $('body').on('click', 'i.add-entrega-parcial', function(){
        $('.entrega_parcial:last').after(
                '<div class="row-fluid entrega_parcial">'+
                '<div class="span1">'+
                    '<div class="control-group">'+
                        '<label class="control-label">Lote</label>'+
                        '<div class="controls">'+
                            '<input type="text" name="lote[]" id="lote" value="" class="span12" disabled="disabled">'+
                            '<input type="hidden" name="id_lote_producao[]" value="" id="id_lote_producao">'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="span2">'+
                    '<div class="control-group">'+
                        '<label class="control-label">Quantidade</label>'+
                        '<div class="controls">'+
                          '<input type="text" name="quantidade[]" value="" class="span12 int quantidade_entrega" style="text-align: right;">'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="span2">'+
                    '<div class="control-group">'+
                        '<label class="control-label">Data e hora de entrega</label>'+
                        '<div class="controls">'+
                          '<input type="text" name="dt_entrega[]" class="dt_entrega span12" value="" >'+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<div class="span6">'+
                    '<div class="control-group">'+
                        '<label class="control-label">Entidade de entrega</label>'+
                        '<div class="controls">'+
                          '<input type="text" name="empresa_entrega[]" class="span8 empresa_entrega">'+
                          '<input type="hidden" name="id_empresa_entrega[]" value="" id="id_empresa_entrega">'+
                          '<i class="icon-remove del-entrega-parcial"></i>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>');
        initEntrega();
    });//end adicionar entrega parcial

    //deletar uma entrega parcial
    $('body').on('click', 'i.del-entrega-parcial', function(){
        $(this).parent().parent().parent().parent().remove();
    });//deketar uma entrega parcial

    //disable button submit form
    $('#form-pedido').submit(function(){
        $('button.btn').addClass('disabled');
    });
    $('body').on('click', 'button.btn.disabled', function(e){
        e.preventDefault();
    });


    //calcula o valor total do processo
    $('body').on('change', '#pro_quantidade, #pro_vlr_unt', function(){
        var pro_vlr_unt = parseFloat($('#pro_vlr_unt').val().replace(/\./g, '').replace(',', '.'));
        var pro_quantidade  = parseFloat( $('#pro_quantidade').val().replace(/\./g, '') );
        $('#pro_vlr_pedido').val( decimal(pro_vlr_unt * pro_quantidade));
        $('.decimal').setMask('decimal');
    });//endValor

    $('body').on('change', '#pro_vlr_pedido', function(){
        if($('#pro_quantidade').val() == '0,00'){
            return
        }
        var pro_vlr_pedido = parseFloat($('#pro_vlr_pedido').val().replace(/\./g, '').replace(',', '.'));
        var pro_quantidade  = parseFloat( $('#pro_quantidade').val().replace(/\./g, '') );
        $('#pro_vlr_unt').val( decimal(pro_vlr_pedido / pro_quantidade));
        $('.decimal').setMask('decimal');
    });

    //codigo de processo
    $('#altera-codigo').blur(function(){
        var cod = $(this).attr('data-pro-codigo');
        var value = $(this).val();
        if(value != cod){
            $(this).val(cod);
        }
    });

    //autocomplete da entidade de entrega
    $( '#altera-codigo' ).autocomplete({
        source: "processo/processo/autocomplete-codigo",
        select: function( event, ui ) {
            window.location.href="processo/processo/form/pro_id/"+ui.item.id;
        }
      });//endautocomplete
});

var FormWizard = function () {

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            var form = $('#form-pedido');
            var error = $('.alert-error', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    nome_razao: {
                        required: true
                    }
                },
                messages: { // custom messages for radio buttons and checkboxes
                    nome_razao: {
                        required: "Campo obrigatório."
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit
                    App.scrollTo(form, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.help-inline').removeClass('ok'); // display OK icon
                    $(element)
                        .closest('.control-group').removeClass('success').addClass('error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change dony by hightlight
                    $(element)
                        .closest('.control-group').removeClass('error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .addClass('valid') // mark the current input as valid and display OK icon
                    .closest('.control-group').removeClass('error').addClass('success'); // set success class to the control group
                },

            });

            // default form wizard
            $('#form_wizard_1').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index) {
                    if (form.valid() == false) {
                        return false;
                    }
                    if ($('#pro_id').val() == "") {
                        return false;
                    }
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard_1')).text('Passo ' + (index + 1) + ' de ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard_1')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard_1').find('.button-previous').hide();
                    } else {
                        $('#form_wizard_1').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard_1').find('.button-next').hide();
                    } else {
                        $('#form_wizard_1').find('.button-next').show();
                    }
                    App.scrollTo($('.caribbean-green'));
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard_1')).text('Passo ' + (index + 1) + ' de ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard_1')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard_1').find('.button-previous').hide();
                    } else {
                        $('#form_wizard_1').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard_1').find('.button-next').hide();
                    } else {
                        $('#form_wizard_1').find('.button-next').show();
                    }

                    App.scrollTo($('.caribbean-green'));
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard_1').find('.bar').css({
                        width: $percent + '%'
                    });

                    // se clickar no tab continuará com o mesmo efeito do proximo
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard_1')).text('Passo ' + (index + 1) + ' de ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard_1')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard_1').find('.button-previous').hide();
                    } else {
                        $('#form_wizard_1').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard_1').find('.button-next').hide();
                    } else {
                        $('#form_wizard_1').find('.button-next').show();
                    }
                    App.scrollTo($('.caribbean-green'));
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard_1')).text('Passo ' + (index + 1) + ' de ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard_1')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard_1').find('.button-previous').hide();
                    } else {
                        $('#form_wizard_1').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard_1').find('.button-next').hide();
                    } else {
                        $('#form_wizard_1').find('.button-next').show();
                    }

                    App.scrollTo($('.caribbean-green'));
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
        }

    };

}();

function initEntrega(){
    $('.int').setMask('integer');
    $('.dt_entrega').setMask('99/99/9999 99:99');
    //aparece o calendário no campo data fim
    $(".dt_entrega").datetimepicker({
        format: "dd/mm/yyyy hh:ii",
        language: 'pt-BR',
        autoclose: true,
        todayBtn: true,
        pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
        minuteStep: 5,
    });

    //autocomplete da entidade de entrega
    $( ".empresa_entrega" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $(this).siblings('input').val(ui.item.id);
        },
        search: function( event, ui ) {
            $(this).siblings('input').val("");
        }
      });//endautocomplete
}


function alertPedido(text){
    $('#alert-pedido span').text(text);
    $('#alert-pedido').dialog({
        modal: true,
        dialogClass: 'ui-dialog-green',
        position: [($(window).width() / 2) - (350 / 2), 200],
        resizable: true,
        title: "Alerta",
        width: 350,
        height: 'auto',
        buttons: [
                  {
                     'class' : 'btn red',
                     "text" : "Fechar",
                    click: function() {
                      $( this ).dialog( "close" );
                    }
                  },
          ],
          close: function(){
                $('#alert-pedido span').html('');
          }
    });
}