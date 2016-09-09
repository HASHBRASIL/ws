$(document).ready(function(){
    $('#especificacao').limit('255','#countEspecif');
    $( "#quantidade" ).spinner({
        min: 0
    });
    $('#quantidade').setMask('999999999');
    $('#table_componente').gridComponente();
    
    //autocomplete trazendo a razão social da empresa ou da pessoa cadastrada
    $( "#nome_empresa_cliente" ).autocomplete({
        source: "/empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_empresa_cliente').val(ui.item.id);
        },
        search: function( event, ui ) {
            $('#id_empresa_cliente').val("");
        }
      });
    
    if($('#id_tp_orcamento').val() != ""){
        var id = $('#id_tp_orcamento').val();

        $.ajax({
            type: "GET",
            url: "/service/orcamento/modelo/id_tp_orcamento/"+id,
            success: function(data){
                if(data.success){
                    $('.notif_modelo').hide('slow');
                }else{
                    $('.notif_modelo').show('slow');
                }
            },
            error: function(e){
                alert("Sistema está fora do ar entre em contato com o administrador.");
            }
        });
    }
    $('.add-tipo').click(function(){
        $('div.dialog_tipo').dialog({
            modal: true,
            resizable: false,
            title: "Adicionar tipo de orçamento",
            position: [($(window).width() / 2) - (400 / 2), 200],
            width: 400,
            height: 190,
            dialogClass: 'ui-dialog-steelblue',
            buttons: [{
            	'class' : 'btn red',
                "text" : "Cancelar",
               click: function() {
                 $( this ).dialog( "close" );
               }
            },{
            	'class' : 'btn green',
                "text" : "Adicionar",
                 click: function() {
                    var nome = $( '#nome_tp' ).val();
                    if(nome == ""){
                        alert('Preencha o nome do tipo de orçamento.');
                        return;
                    }

                    $.ajax({
                        type: "POST",
                        url: "/service/tipo-orcamento/form",
                        data: {'nome': nome},
                        success: function(data){
                            if(data.success){
                                $.messageBox("Tipo de orçamento inserido com sucesso.", 'success');
                                $('div.dialog_tipo').dialog( 'close' );
                                $('#id_tp_orcamento').append('<option value="'+data.id.id_tp_orcamento+'">'+nome+"</option>");
                                $('#id_tp_orcamento').val(data.id.id_tp_orcamento);
                                $('.notif_modelo').show();
                            }else{
                                alert(data.mensagem[0].text);
                            }
                        },
                        error: function(e){
                            alert("Sistema está fora do ar entre em contato com o administrador.");
                        }
                    });
                }
              }],
              open: function(){
              },
              close: function(){
                  $( '#nome_tp' ).val('');
              }
        });
    });
    
    $('#id_tp_orcamento').change(function(){
        var id = $('#id_tp_orcamento').val();

        $.ajax({
            type: "GET",
            url: "/service/orcamento/modelo/id_tp_orcamento/"+id,
            success: function(data){
                if(data.success){
                    $('.notif_modelo').hide('slow');
                }else{
                    $('.notif_modelo').show('slow');
                }
            },
            error: function(e){
                alert("Sistema está fora do ar entre em contato com o administrador.");
            }
        });
    });
    
    $('body').on('click', 'a.new-data', function(e){
        e.preventDefault();
        $('div.dialog_componente').dialog({
            modal: true,
            resizable: false,
            title: "Adicionar componente",
            position: [($(window).width() / 2) - (450 / 2), 200],
            width: 450,
            height: 560,
            buttons: [
                      {
                          'class' : 'btn red',
                          "text" : "Cancelar",
                         click: function() {
                           $( this ).dialog( "close" );
                         }
                       },
                       {
                          'class' : 'btn blue',
                          "text" : "Enviar",
                           click: function() {
                    var id_servico          = $( '#id_servico' ).val();
                    var id_tp_servico       = $('#id_tp_servico').val();
                    var id_tp_componente    = $('#id_tp_componente').val();
                    var quantidade_valor    = $('#quantidade_valor').val();
                    var id_valor_servico    = $('#id_valor_servico').attr('id_valor_servico');
                    if(id_servico == ""){
                        alert('Selecione um serviço.');
                        return;
                    }
                    if(id_tp_servico == ""){
                        alert('Selecione um tipo de serviço.');
                        return;
                    }
                    if(id_tp_componente == ""){
                        alert('Selecione o componente.');
                        return;
                    }
                    if(quantidade_valor == "" || quantidade_valor == 0){
                        alert('O campo quantidade de valor é obrigatório');
                        return;
                    }
                    if(id_valor_servico == "" || quantidade_valor == 0){
                        alert('O selecione um valor unitário.');
                        return;
                    }

                    $.ajax({
                        type: "POST",
                        url: "/service/componente/form",
                        data: {'id_servico': id_servico, 'id_orcamento' : $('#id_orcamento').val(),
                               'id_tp_servico': id_tp_servico, 'id_tp_componente' : id_tp_componente,
                               'quantidade': quantidade_valor, 'id_valor_servico': id_valor_servico,
                               'caracteristica':$('#caracteristica').val()},
                        success: function(data){
                            if(data.success){
                                $.messageBox("Componente inserido com sucesso.", 'success');
                                $('#table_componente').gridComponente();
                                $('div.dialog_componente').dialog( 'close' );
                            }else{
                                alert(data.mensagem[0].text);
                            }
                        },
                        error: function(e){
                            alert("Sistema está fora do ar entre em contato com o administrador.");
                        }
                    });
                }
                }
             ],
              open: function(){
                  $('#quantidade_valor').setMask('999999999999');
                  $('#id_valor_servico, #valor').setMask('decimal');
                  //autocomplete trazendo o serviço
                  $( "#nome_servico" ).autocomplete({
                      source: "/service/service/autocomplete",
                      minLength: 2,
                      select: function( event, ui ) {
                          $('#id_servico').val(ui.item.id);
                      },
                      search: function( event, ui ) {
                          $('#id_servico').val("");
                      }
                    });
              },
              close: function(){
                  $('form.form_componente input,form.form_componente select,form.form_componente textarea').val('');
              }
        });
    });
    
    $('.add_tp_componente').click(function(){
        $('div.dialog_tipo').dialog({
            modal: true,
            resizable: false,
            title: "Adicionar tipo de componente",
            position: [($(window).width() / 2) - (400 / 2), 300],
            width: 400,
            height: 200,
            buttons: [
                      {
                          'class' : 'btn red',
                          "text" : "Cancelar",
                         click: function() {
                           $( this ).dialog( "close" );
                         }
                       },
                       {
                          'class' : 'btn blue',
                          "text" : "Enviar",
                           click: function() {
                    var nome = $( '#nome_tp' ).val();
                    if(nome == ""){
                        alert('Preencha o nome do tipo de componente.');
                        return;
                    }

                    $.ajax({
                        type: "POST",
                        url: "/service/tipo-componente/form",
                        data: {'nome': nome},
                        success: function(data){
                            if(data.success){
                                $.messageBox("Tipo de orçamento inserido com sucesso.", 'success');
                                $('div.dialog_tipo').dialog( 'close' );
                                $('#id_tp_componente').append('<option value="'+data.id.id_tp_componente+'">'+nome+"</option>");
                                $('#id_tp_componente').val(data.id.id_tp_componente);
                                $('.notif_modelo').show();
                            }else{
                                alert(data.mensagem[0].text);
                            }
                        },
                        error: function(e){
                            alert("Sistema está fora do ar entre em contato com o administrador.");
                        }
                    });
                           }
                       }
              ],
              open: function(){
              },
              close: function(){
                  $( '#nome_tp' ).val('');
              }
        });
    });
    
    $('#add_valor').on('click', function(){
        var id_servico      = $('#id_servico').val();
        if(!$.isNumeric(id_servico)){
            alert('Selecione um serviço.');
            return;
        }
        
        $('div#dialog_valor').dialog({
            modal: true,
            resizable: true,
            title: "Valor Únitario",
            position: [($(window).width() / 2) - (500 / 2), 300],
            width: 500,
            height: 360,
              open: function(){
                  $('div#dialog_valor').valorServico();
              },
              close: function(){
              }
        });
        
    });
    $('#quantidade_valor').change(function(){
        $('#total').total();
    });
    $('body').on('click', '.add_valor_unitario', function(){
        var id_valor_servico = $(this).attr('id_valor_servico');
        var fixo             = $(this).attr('fixo');
        var valor_unitario   = $(this).attr('valor_unitario');
        var id_empresa       = $(this).attr('id_empresa');
        $('#id_valor_servico').val(valor_unitario).attr('fixo', fixo).attr('id_valor_servico', id_valor_servico);
        if(fixo == '1'){
            $('#quantidade_valor').attr('disabled', 'disabled').val(1);
            $('#total').total();
        }else{
            $('#quantidade_valor').removeAttr('disabled').val('');
            $('#total').val('');
        }
        if($.isNumeric(id_empresa)){
            $('#id_tp_servico').val(2);
        }else{
            $('#id_tp_servico').val(1);
        }
        $('div#dialog_valor').dialog('close');
    });
    
    $('#id_tp_servico').change(function(){
        $('#id_valor_servico').val('').removeAttr('fixo').removeAttr('id_valor_servico');
        $('#quantidade_valor').removeAttr('disabled').val('');
        $('#total').val('');
    });
});

$.fn.gridComponente = function(){
    var id_orcamento = $('#id_orcamento').val();
    var $this = $(this);
    if($.isNumeric(id_orcamento) == false){
        return;
    }

    $.ajax({
        type: "GET",
        url: "/service/componente/grid/id_orcamento/"+id_orcamento,
        success: function(data){
            $this.html(data);
        },
        error: function(e){
            alert("Sistema está fora do ar entre em contato com o administrador.");
        }
    });
};

$.fn.valorServico = function(){
    var id_servico      = $('#id_servico').val();
    var id_tp_servico   = $('#id_tp_servico').val();
    var $this           = $(this);
    if(!$.isNumeric(id_servico)){
        return;
    }

    $.ajax({
        type: "GET",
        url: "/service/valor-servico/grid-valor/id_servico/"+id_servico+"/id_tp_servico/"+id_tp_servico,
        success: function(data){
            $this.html(data);
            var width   = $('#dialog_valor table').width();
            var height  = $('#dialog_valor table').height()+40;
            $this.dialog( "option", "width", width );
            $this.dialog( "option", "height", height );
        },
        error: function(e){
            alert("Sistema está fora do ar entre em contato com o administrador.");
        }
    });
};

$.fn.total = function(){
    var id_valor_servico      = $('#id_valor_servico').val();
    var quantidade_valor      = $('#quantidade_valor').val();
    var $this                 = $(this);
    if(id_valor_servico == ""){
        return;
    }
    if(!$.isNumeric(quantidade_valor)){
        return;
    }

    var total   = id_valor_servico.replace(',', '.')*quantidade_valor;
    total       = total.toFixed(2).toString().replace('.', ',');
    $this.val(total).setMask('decimal');
};