$(document).ready(function(){
    var $interno = $('#tipo_interno');
    var $externo = $('#tipo_externo');
    
    $('#descricao').limit('255','#countDescricao');

    check($interno, $('.centro_custo'));
    check($externo, $('.fornecedor'));
    check($interno, $('#novoForm'));
    check($interno, $('#grid-tarefa'));
    
    $( "#unidade" ).spinner({
        min: 0
    });
    $('#unidade').setMask('999999999');
    
    $( "#minuto" ).spinner({
        min: 0
    });
    $('#minuto').setMask('999999999');
    //carrega a tabela da tarefa
    gridTarefa();
    //interno qto o combo é clicado
    $interno.click(function(e){
        if($interno.is(':checked')){
            $('.centro_custo').show();
            $('#novoForm').show();
            $('#grid-tarefa').show();
        }else{
            if($('.centro_custo > input').length > 0 || ($('.dataTables_empty').length == 0 && $('#datatable').length > 0)){
                var valid = confirm("Gostaria de remover todos os centro de custo e tarefas selecionados ?");
                if(valid){
                    $('.centro_custo > input').remove();
                    $('.centro_custo > img').remove();
                    $('.centro_custo').hide();
                    $('#novoForm').hide();
                    $('#grid-tarefa').hide();
                    deleteAllTarefa();
                }else{
                    e.preventDefault();
                    return;
                }
            }else{
                $('.centro_custo').hide();
                $('#novoForm').hide();
                $('#grid-tarefa').hide();
            }
        }
    });
    
    $externo.click(function(e){
        if($externo.is(':checked')){
            $('.fornecedor').show();
        }else{
            if($('.fornecedor > input').length > 1){
                var valid = confirm("Gostaria de remover todos os fornecedores selecionados?");
                if(valid){
                    $('.fornecedor > input').remove();
                    $('.fornecedor > img').remove();
                    $('.fornecedor').hide();
                }else{
                    e.preventDefault();
                    return;
                }
            } else {
                $('.fornecedor').hide();
            }
        }
    });
    
    //adicionando um centro de custo abrindo um dialog com autocomplete
    $('.add_centro_custo').click(function(){
        $('#diallog-autocomplete').dialog({
            modal: true,
            resizable: false,
            title: "Adicionar centro de custo",
            position: [($(window).width() / 2) - (400 / 2), 200],
            width: 350,
            height: 170,
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
                    if($('#autocomplete_id').val() == ""){
                        alert('selecione um centro de custo válido.');
                        return;
                    }
                    if(existIdCentroCusto( $('#autocomplete_id').val() )){
                        alert('Centro de custo já adicionado.');
                        return;
                    }
                  var value = $( "#autocomplete" ).val();
                  var id    = $('#autocomplete_id').val();
                  $('.centro_custo').append("<input type='text' disabled='disabled' value='"+value+"' class='centro_custo_"+id+"' style='width:80%; display: inline-block;' />")
                                    .append("<img src='/images/delete.png' class='delete_centro_custo' id-centro-custo='"+id+"'  title='Deletar centro de custo' data-tooltip />");
                  $('.centro_custo').append("<input type='hidden' value='"+id+"' name='id_centro_custo[]' class='centro_custo_"+id+"' />");
                  $( this ).dialog( "close" );
                }
              }
            ],
              open: function(){
                  autocomplete("/service/centro-custo/autocomplete");
              },
              close: function(){
                  $('#autocomplete_id').val("");
                  $( "#autocomplete" ).val("");
              }
        });
    });

    //adicionando um fornecedor abrindo um dialog com autocomplete
    $('.add_fornecedor').click(function(){
        $('#diallog-autocomplete').dialog({
            modal: true,
            resizable: false,
            position: [($(window).width() / 2) - (400 / 2), 200],
            title: "Adicionar fornecedor",
            width: 350,
            height: 170,
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
                    if($('#autocomplete_id').val() == ""){
                        alert('selecione um fornecedora válido.');
                        return;
                    }
                    if(existIdFornecedor( $('#autocomplete_id').val() )){
                        alert('Fornecedor já adicionado.');
                        return;
                    }
                  var value = $( "#autocomplete" ).val();
                  var id    = $('#autocomplete_id').val();
                  $('.fornecedor').append("<input type='text' disabled='disabled' value='"+value+"' class='fornecedor_"+id+"' style='width:80%; display: inline-block;'/>")
                                  .append("<img src='/images/delete.png' class='delete_fornecedor' id-fornecedor='"+id+"' data-tooltip title='Deletar fornecedor' />");
                  $('.fornecedor').append("<input type='hidden' value='"+id+"' name='id_empresa_fornecedor[]' class='fornecedor_"+id+"'/>");
                  $( this ).dialog( "close" );
                }
              }
            ],
            open: function(){
                autocomplete("/empresa/empresa/fornecedor-json");
            },
            close: function(){
                $('#autocomplete_id').val("");
                $( "#autocomplete" ).val("");
            }
        });
    });
    
    //deleta o fornecedor
    $("body").on("click", ".delete_fornecedor", function(event){
        var id_fornecedor = $(this).attr("id-fornecedor");
        $(this).remove();
        $('.tooltip').hide();
        $('.fornecedor_'+id_fornecedor).remove();
      });

    //deleta o centro de custo
    $("body").on("click", ".delete_centro_custo", function(event){
        var id = $(this).attr("id-centro-custo");
        $(this).remove();
        $('.tooltip').hide();
        $('.centro_custo_'+id).remove();
      });
    
    // cria um dialog para cadastar a tarefa
    $('#novoForm').click(function(e){
        e.preventDefault();
        if($('#id_servico').val() == ""){
            $.messageBox("Cadastre um serviço para criar uma tarefa.", 'alert');
            return;
        }
        $('#dialog-form').dialog({
            title: "Cadastrar tarefa",
            modal: true,
            position: [($(window).width() / 2) - (400 / 2), 200],
            width: 400,
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
                    var nome = $('#nome_tarefa').val();
                    var minuto = $('#minuto').val();
                    var id_tarefa = $('#id_tarefa').val();
                    if( validar(nome, "O campo nome está vazio") || validar(minuto, "O campo minuto está vazio.") ){
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: "/service/tarefa/form",
                        data: {'nome': nome, 'tempo_extimado' : minuto,
                               'id_servico': $('#id_servico').val(),
                               'id_tarefa' : id_tarefa },
                        success: function(e){
                            if(e.success){
                                if(id_tarefa == ""){
                                    $.messageBox("Tarefa inserido com sucesso.", 'success');
                                }else{
                                    $.messageBox("Tarefa atualizado com sucesso.", 'success');
                                }
                                gridTarefa();
                                $('#dialog-form').dialog('close');
                            }else{
                                alert('Não foi possivel salvar no momento.');
                            }
                        },
                        error: function(e){
                            alert("Sistema está fora do ar entre em contato com o administrador.");
                        }
                    });
                }
            }
            ],
        close: function(){
            $('#minuto').val('');
            $('#nome_tarefa').val('');
            $('#id_tarefa').val("");
        }
        });
    }); 
    // fechar o dialog
    
    
    // cria um dialog para cadastar a unidade
    $('.add-unidade').click(function(e){
        e.preventDefault();
        $('#dialog-form-unidade').dialog({
            title: "Cadastrar tipo de unidade",
            position: [($(window).width() / 2) - (400 / 2), 200],
            modal: true,
            width: 400,
            dialogClass: 'ui-dialog-steelblue',
            buttons: [{
            	'class' : 'btn red',
                "text" : "Cancelar",
               click: function() {
                 $( this ).dialog( "close" );
               }
            },{
            	'class' : 'btn green',
                "text" : "Salvar",
                 click: function() {
                    var nome = $('#nome-unidade').val();
                    if( validar(nome, "O campo nome está vazio") ){
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: "/service/tipo-unidade/form",
                        data: {'nome': nome },
                        success: function(e){
                            if(e.success){
                                $.messageBox("Tipo de unidade inserido com sucesso.", 'success');
                                $('#id_tipo_unidade').append('<option value="'+e.id.id_tipo_unidade+'" selected="selected">'+nome+'</option>');
                                $('#dialog-form-unidade').dialog('close');
                            }else{
                                alert('Não foi possivel salvar no momento.');
                            }
                        },
                        error: function(e){
                            alert("Sistema está fora do ar entre em contato com o administrador.");
                        }
                    });
                }
            }
            ],
        close: function(){
            $('#nome-unidade').val('');
        }
        });
    }); 
    // fechar o dialog
    
    
    //editar tarefa
    $("body").on("click", ".editar-tarefa", function(e){
        e.preventDefault();
        var id_tarefa = $(this).attr("href");
        var nome         = $(this).attr('name');
        var tempo        = $(this).attr("tempo");

        $('#minuto').val(tempo);
        $('#nome_tarefa').val(nome);
        $('#id_tarefa').val(id_tarefa);
        $("#novoForm").trigger("click");
      });
    
    $("body").on("click", ".deletar-tarefa", function(e){
        e.preventDefault();
        var result = confirm("Deseja Realmente excluir este dado?");
        if(!result){
            return;
        }
        var href = $(this).attr("href");
        $.ajax({
            type: "GET",
            url: href,
            success: function(e){
                if(e[0].type == "success"){
                    $.messageBox(e[0].text, 'success');
                }else{
                    $.messageBox(e[0].text, 'success');
                }
                gridTarefa();
            },
            error: function(e){
                alert("Sistema está fora do ar entre em contato com o administrador.");
            }
        });
    });
    
    $('#button_valor').on('click', function(e){
        e.preventDefault();

        $('#dialog-form-valor').dialog({
            modal: true,
            resizable: true,
            position: [($(window).width() / 2) - (300 / 2), 200],
            title: "Valor do Serviço",
            dialogClass: 'ui-dialog-steelblue',
            width: 300,
            height: 300,
            open: function(){
                $('#vl_unitario').setMask('decimal');
            },
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
                    var vl_unitario     = $('#vl_unitario').val();
                    var fixo            = $('#fixo').is(':checked') == 1? 1:0;
                    var nome_empresa    = $('#id_empresa_vl').val() == ''? 'Interno':$('#id_empresa_vl option:selected').text() ;
                    
                    if(vl_unitario == ""){
                        alert('Preencha o campo valor unitário.');
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: '/service/valor-servico/form',
                        data: {'vl_unitario' : vl_unitario, 'fixo': fixo, 'id_empresa': $('#id_empresa_vl').val(), 'id_servico': $('#id_servico').val(),id:$('#id_valor_servico').val()},
                        success: function(data){
                            if( data.success){
                            	window.location.reload(true);
                            }
                        },
                        error: function(){
                            alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                        }
                        
                    });
                 }
                }
              ],
              close: function(){
                  $('#fixo').attr('checked', false);
                  $('#vl_unitario').val('');
                  $('#id_empresa_vl').val('');
                  $('#id_valor_servico').val('');
              }
        });
    });
    
    $('body').on('click','.editar_valor', function(e){
        e.preventDefault();
        var id_valor_servico = $(this).attr('id_valor_servico');
        $.ajax({
            type: "GET",
            url: '/service/valor-servico/get/id/'+id_valor_servico,
            success: function(data){
                if(data.fixo == 1){
                    $('#fixo').trigger('click');
                }
                $('#vl_unitario').val(data.vl_unitario);
                $('#id_empresa_vl').val(data.id_empresa);
                $('#button_valor').trigger('click');
                $('#id_valor_servico').val(data.id_valor_servico);
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
            
        });
    });
    
    $('body').on('click','a.delete_valor ', function(e){
        e.preventDefault();
        var id_valor_servico = $(this).attr('id_valor_servico');
        $.ajax({
            type: "GET",
            url: '/service/valor-servico/delete/id/'+id_valor_servico,
            success: function(data){
                if( data[0].type == "success" ){
                    $('.valor_'+id_valor_servico).remove();
                    $.messageBox("valor de serviço excluído com sucesso.", 'success');
                    $('span.tooltip').hide();
                }else{
                    alert(data.mensagem[0].text);
                }
                
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
            
        });
    });
});

function autocomplete(url){
    $( "#autocomplete" ).autocomplete({
        source: url,
        minLength: 2,
        select: function( event, ui ) {
            $('#autocomplete_id').val(ui.item.id);
        },
        search: function( event, ui ) {
            $('#autocomplete_id').val("");
        }
      });
}

function check( $tipo, $div){
    if($tipo.is(':checked')){
        $div.show();
    }else{
        $div.hide();
    }
}

function gridTarefa(){
    var id_servico = $('#id_servico').val();
    if(id_servico != ""){
        $.ajax({
            type: 'GET',
            url: "/service/tarefa/grid/id_servico/"+id_servico,
            success: function(data){
                $('#grid-tarefa').html(data);
            },
            error: function(){
                alert("Não foi possivel carregar a tabela de tarefa. Tente novamente mais tarde.");
            }
        });
    }
}

function validar(value, mensagem){
    if(value == ""){
        alert(mensagem);
        return true;
    }
    return false;
}

//verifica se o id ja existe no centro de custo
function existIdCentroCusto(valueId){
    var retorno = false;
    if($('input[name="id_centro_custo[]"]').length > 0){
    $.each( $('input[name="id_centro_custo[]"]'), function( key, value ) {
            if($(this).val() == valueId){
                retorno = true;
            }
        });
    }
    return retorno;
}

//verifica se o id ja existe no fornecedor
function existIdFornecedor(valueId){
    var retorno = false;
    if($('input[name="id_empresa_fornecedor[]"]').length > 0){
    $.each( $('input[name="id_empresa_fornecedor[]"]'), function( key, value ) {
            if($(this).val() == valueId){
                retorno = true;
            }
        });
    }
    return retorno;
}

function  deleteAllTarefa(){
    var id_servico = $('#id_servico').val();
    $.ajax({
        type: "GET",
        url: "/service/tarefa/delete-all/id_servico/"+id_servico,
        success: function(e){
            gridTarefa();
        },
        error: function(e){
            alert("Sistema está fora do ar entre em contato com o administrador.");
        }
    });
}