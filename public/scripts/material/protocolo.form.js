$(document).ready(function(){
	
	$("#gerarRecibo").on("click", function(event){
		
		$('#dialog_recibo').dialog({
	        modal: true,
	        open: function(event, ui) {
	        	
	        },
	        title: "Criar vínculo financeiro com este processo",
	        position: { my: "center", at: "top", of: window},
	        width: "500",
	        height: "200",
	        buttons: {
	            "Fechar": function() {
	              $( this ).dialog( "close" );
	              
	            },
	            "Gerar": function() {
		            
	            	var idProtocolo = $('#id_protocolo').val();
	            	
		            var saldo = 0;
	                 var trel;
	                 if($('#saldo').is(':checked')){
	                     saldo = 1;
	                 }
	                 $('input.trel').each(function() {
	                     //Verifica qual está selecionado
	                     if ($(this).is(':checked'))
	                         trel = parseInt($(this).val());
	                 });
	                 //location.href = "/material/protocolo/relatorio/trel/"+trel+"/saldo/"+saldo+"/id_protocolo/"+$('#id_protocolo').val();
	                 window.open("/material/protocolo/relatorio/trel/"+trel+"/saldo/"+saldo+"/id_protocolo/"+idProtocolo, '_blank');
	                 $(this).dialog('close');
		        },
	          },
	          close: function(){
	          }
	    });
		
	});
	
    $( "#dt_entrada" ).datepicker();
    //$('#hr_entrada').timepicker({showSecond: true,timeFormat: 'HH:mm:ss'});
    $("#dt_entrada").setMask({mask:'99/99/9999',autoTab: false});
    $("#hr_entrada").setMask({mask:'99:99:99',autoTab: false});
    $("#cnpj_fornecedor, #cnpj_receptora, #cnpj_transportador").setMask({mask:'99.999.999/9999-99',autoTab: false});
    //unsetMask() PARA RETIRAR A MASCARA JQUERY
    $('.decimal').setMask();
    $("#id_tp_transportador").selectTransportador();
    
    $( "#empresa_fornecedor" ).autocomplete({
        source: "/empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_empresa_fornecedor').val(ui.item.id);
            $(this).camposFornecedor('razao');
        },
        search: function( event, ui ) {
            $('#id_empresa_fornecedor').val("");
            $(this).camposFornecedor('razao');
        }
      });
    $( "#cnpj_fornecedor" ).autocomplete({
        source: "/empresa/empresa/autocomplete-cnpj",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_empresa_fornecedor').val(ui.item.id);
            $(this).camposFornecedor('CNPJ');
            $("#cnpj_fornecedor").unsetMask();
        },
        search: function( event, ui ) {
            $('#id_empresa_fornecedor').val("");
            $(this).camposFornecedor('CNPJ');
            $("#cnpj_fornecedor").setMask({mask:'99.999.999/9999-99',autoTab: false});
        }
      });
    
    
    //adicionando campo de protocolo do fornecedor
    $('body').on('click', 'i.add_controle_fornecedor', function(){
        $(this).parent().append('<input type="text" name="controle_fornecedor[]" class="span8" >'+
                        '<i class="remove_controle_fornecedor icon-remove" title="Remover controle do receptor"></i>');
    });
    //remove o campo de protocolo do fornecedor
    $('body').on('click', 'i.remove_controle_fornecedor', function(){
        $(this).prev('input').remove();
        $(this).remove();
        $('.tooltip').hide();
    });
    
    //adicionando campo de protocolo do fornecedor
    $('body').on('click', 'i.add_controle_receptora', function(){
        $(this).parent().append('<input type="text" name="controle_receptora[]" value="" class="span8">'+
                        '<i class="remove_controle_receptora icon-remove" title="Remover controle do receptor"></i>');
    });
    //remove o campo de protocolo do fornecedor
    $('body').on('click', 'i.remove_controle_receptora', function(){
        $(this).prev('input').remove();
        $(this).remove();
        $('.tooltip').hide();
    });
    
    //autocomplete da empresa receptora
    $( "#empresa_receptora" ).autocomplete({
        source: "/empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_empresa_receptora').val(ui.item.id);
            $('#id_empresa_receptora').camposReceptora('receptora');
        },
        search: function( event, ui ) {
            $('#id_empresa_receptora').val("");
            $('#id_empresa_receptora').camposReceptora('receptora');
        }
      });
    
    $( "#cnpj_receptora" ).autocomplete({
        source: "/empresa/empresa/autocomplete-cnpj",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_empresa_receptora').val(ui.item.id);
            $('#id_empresa_receptora').camposReceptora('CNPJ');
            $("#cnpj_receptora").unsetMask();
        },
        search: function( event, ui ) {
            $('#id_empresa_receptora').val("");
            $('#id_empresa_receptora').camposReceptora('CNPJ');
            $("#cnpj_fornecedor").setMask({mask:'99.999.999/9999-99',autoTab: false});
        }
      });
    
    //autocomplete da empresa transportadora
    $( "#empresa_transportador" ).autocomplete({
        source: "/empresa/empresa/transportador-json",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_transp_empresa').val(ui.item.id);
            $('#id_transp_empresa').camposTransportadora('transportador');
        },
        search: function( event, ui ) {
            $('#id_transp_empresa').val("");
            $('#id_transp_empresa').camposTransportadora('transportador');
        }
      });
    
    $( "#cnpj_transportador" ).autocomplete({
        source: "/empresa/empresa/transportador-cnpj",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_transp_empresa').val(ui.item.id);
            $('#id_empresa_transportador').camposTransportadora('CNPJ');
            $("#cnpj_transportador").unsetMask();
        },
        search: function( event, ui ) {
            $('#id_transp_empresa').val("");
            $('#id_empresa_transportador').camposTransportadora('CNPJ');
            $("#cnpj_transportador").setMask({mask:'99.999.999/9999-99',autoTab: false});
        }
      });
    
    //carrega a grid de produtos
    $('#table_estoque').gridItem();
    
    //adicionar tipo de entrada no protocolo
    $('body').on('click', 'img.add_tp_protocolo', function(){

        $('div.add_tipo').dialog({
            modal: true,
            resizable: true,
            position: [($(window).width() / 2) - (280 / 2), 200],
            title: "Adicionar tipo de entrada",
            width: 280,
            height: 180,
            open: function(){
            },
            buttons: {
                "Cancelar": function() {
                  $( 'div.add_tipo' ).dialog( "close" );
                },
                "Adicionar": function() {
                    var nome         = $('#nome_tp_entrada').val();
                    var tp_movimento = $('#tp_movimentacao').val();
                    if(nome == ""){
                        alert('O campo nome é obrigatório.');
                        return;
                    }
                    if(tp_movimento == "")
                    {
                        alert('Selecione um tipo de movimento.');
                        return;
                    }
                        
                    $.ajax({
                        type: "POST",
                        url: '/material/tipo-entrada/form',
                        data: {'nome' : $('#nome_tp_entrada').val(), 'id_tp_movimento' : tp_movimento},
                        success: function(data){
                            if( data.success){
                                $.messageBox("Tipo de entrada adicionado com sucesso.", 'success');
                                $('#id_tp_protocolo').append('<option value="'+data.id.id_tp_protocolo+'" >'+nome+"</option>").val(data.id.id_tp_protocolo);
                                $( 'div.add_tipo' ).dialog( "close" );
                            }else{
                                alert(data.mensagem[0].text);
                            }
                        },
                        error: function(){
                            alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                        }
                        
                    });
                    
                }
              },
              close: function(){
                  $('#nome_tp_entrada').val('');
                  $('#tp_movimentacao').val('');
              }
        });
    });
    
    //mostra os lotes gerados
    $('body').on('click', 'a.ver_lote', function(e){
        e.preventDefault();
        var id_movimento = $(this).attr('href');
        var id_tp_movimento = $(this).attr('id_tp_movimento');
        $('div#dialog_ver_lote').dialog({
            modal: true,
            resizable: true,
            position: [($(window).width() / 2) - (650 / 2), 150],
            title: "Lote",
            width: 650,
            height: 'auto',
            dialogClass: 'ui-dialog-purple dialog-lote',
            open: function(){
                $('div#dialog_ver_lote').html("<img src='/images/loading.gif' style='margin-left: 240px;margin-top: 200px;'></img>");
            },
            close: function(){
                $('div#dialog_ver_lote').html("");
            }
        });
        $.ajax({
            type: "GET",
            url: '/material/movimento/grid-lote/id_movimento/'+id_movimento+'/id_tp_movimento/'+id_tp_movimento,
            success: function(data){
                $('div#dialog_ver_lote').html(data);
            }
        });
    });
    
    $('body').on('click', 'img.add_grupo_operacao', function(e){
        $select = $(this).siblings('select');
        $('div.dialog_grupo_operacao').dialog({
            modal: true,
            resizable: true,
            position: [($(window).width() / 2) - (350 / 2), 150],
            title: "Cadastro de grupo de operação",
            width: 350,
            height: 380,
            open: function(){
                $('#ope_telefone1').maskTelefone();
            },
            buttons: {
                "Cancelar": function() {
                  $( this ).dialog( "close" );
                },
                "Adicionar": function() {
                    if($('#empresas_grupo_id').val() == ""){
                        alert('Selecione uma empresa do grupo.');
                        return;
                    }
                    if($('#ope_nome').val() == ""){
                        alert('Preencha o nome da operação.');
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: '/empresa/operacao/form',
                        data: $('.form_grupo_operacao').serialize(),
                        success: function(data){
                            if( data.success){
                                alert("Grupo de Operação cadastrado com sucesso.");
                                $('#id_operacao_requisitante, #id_operacao_requisitado').append('<option value="'+data.id.ope_id+'">'+$('#ope_nome').val()+'</option>');
                                $select.val(data.id.ope_id);
                                $( 'div.dialog_grupo_operacao' ).dialog( "close" );
                            }else{
                                alert(data.mensagem[0].text);
                            }
                        },
                        error: function(){
                            alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                        }
                    });
                    
                }
              },
            close: function(){
                $('#ope_nome').val('');
                $('#empresas_grupo_id').val('');
                $('#ope_cpf_cnpj').val('');
                $('#ope_telefone1').val('');
                $('#ope_email1').val('');
                $('.delete_tel_ope, #ope_telefone2, #ope_telefone3').remove();
                $('img.add_tel_ope').css('display','inline-block');
                $('.delete_email_ope, #ope_email').remove();
                $('img.add_email_ope').css('display','inline-block');
            }
        });
    });

    //add um campo de telefone no maximo tres campos no form de operação
    $('body').on('click', 'img.add_tel_ope', function(){
        if($('#ope_telefone2').length == 0 ){
            $('div.ope_tel').append("<input type='text' name='ope_telefone2' id='ope_telefone2' class='telefone' style='display:inline-block; width:90%;'>").
                              append(" <img src='/images/delete.png' data-tooltip title='excluir telefone' class='delete_tel_ope' id_tel_ope='2' style='cursor:pointer'>");
        }else if($('#ope_telefone3').length == 0){
            $('div.ope_tel').append("<input type='text' name='ope_telefone3' id='ope_telefone3' class='telefone' style='display:inline-block; width:90%;'>").
                              append(" <img src='/images/delete.png' data-tooltip title='excluir telefone' class='delete_tel_ope' id_tel_ope='3' style='cursor:pointer'>");
            $('img.add_tel_ope').hide('slow');
        }
        var height = $('div.dialog_grupo_operacao').dialog( "option", "height" );
        $('div.dialog_grupo_operacao').dialog( "option", "height", height+50 );
        $('.telefone').maskTelefone();
    });
    
    //apaga o campo de telefone clicado no form de operação
    $('body').on('click', '.delete_tel_ope', function(){
        var id_tel = $(this).attr('id_tel_ope');
        $(this).remove();
        $('#ope_telefone'+id_tel).remove();
        $('.tooltip').hide();
        $('img.add_tel_ope').css('display','inline-block');
        var height = $('div.dialog_grupo_operacao').dialog( "option", "height" );
        $('div.dialog_grupo_operacao').dialog( "option", "height", height-50 );
    });


    //add um campo de email no maximo tres campos no form de operação
    $('body').on('click', 'img.add_email_ope', function(){
        if($('#ope_telefone2').length == 0 ){
            $('div.ope_email').append("<input type='text' name='ope_email2' id='ope_email2' class='email_ope' style='display:inline-block; width:90%;'>").
                              append(" <img src='/images/delete.png' data-tooltip title='excluir email' class='delete_email_ope' id_email_ope='2' style='cursor:pointer'>");
            $('img.add_email_ope').hide('slow');
        }
        var height = $('div.dialog_grupo_operacao').dialog( "option", "height" );
        $('div.dialog_grupo_operacao').dialog( "option", "height", height+50 );
    });
    
    //apaga o campo de telefone clicado no form de operação
    $('body').on('click', '.delete_email_ope', function(){
        var id_email = $(this).attr('id_email_ope');
        $(this).remove();
        $('#ope_email'+id_email).remove();
        $('.tooltip').hide();
        $('img.add_email_ope').css('display','inline-block');
        var height = $('div.dialog_grupo_operacao').dialog( "option", "height" );
        $('div.dialog_grupo_operacao').dialog( "option", "height", height-50 );
    });
    
    // se tiver mais do que 14 caracteres o campo de login ele irá mudar a mascara para cnpj
    $('#ope_cpf_cnpj').keyup(function(event) {
        var cpf = $('#ope_cpf_cnpj').unmaskedVal();
        if(cpf.length > 14){
            $('#ope_cpf_cnpj').setMask("99.999.999/9999-99");
        }else{
            $('#ope_cpf_cnpj').setMask("999.999.999-999");
        }
    });
});

//começando as functions

$.fn.camposFornecedor = function(type){
    var id_fornecedor =  $('#id_empresa_fornecedor').val();
    
    if(id_fornecedor == ""){
        if(type == "CNPJ"){
            $('#empresa_fornecedor').val('');
        }else if(type == "razao"){
            $('#cnpj_fornecedor').val('');
        }
    }else {
        //busca os dados do destinatário
        $.ajax({
            type: "GET",
            url: '/empresa/empresa/get/id/'+id_fornecedor,
            success: function(data){
                if(type == "CNPJ"){
                    $('#empresa_fornecedor').val(data.nome_razao);
                }else if(type == "razao"){
                    $('#cnpj_fornecedor').maskCnpj(data.cnpj_cpf);
                }
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
            
        });

    }

};

//campos grupos
$.fn.camposReceptora = function(type){
    var id_empresa_receptora =  $('#id_empresa_receptora').val();
    
    if(id_empresa_receptora == ""){
        if(type == 'CNPJ'){
            $('#empresa_receptora').val('');
        }else{
            $('#cnpj_receptora').val('');
        }
    }else {
        //busca os dados do destinatário
        $.ajax({
            type: "GET",
            url: '/empresa/empresa/get/id/'+id_empresa_receptora,
            success: function(data){
                if(type == 'CNPJ'){
                    $('#empresa_receptora').val(data.nome_razao);
                }else{
                    $('#cnpj_receptora').maskCnpj(data.cnpj_cpf);
                }
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
            
        });

    }

};

//campos grupos
$.fn.camposTransportadora = function(type){
    var id_empresa_transportador =  $('#id_transp_empresa').val();
    
    if(id_empresa_transportador == ""){
        if(type == 'CNPJ'){
            $('#empresa_transportador').val('');
            $('#id_transp_empresa').val('');
        }else{
            $('#cnpj_transportador').val('');
            $('#id_transp_empresa').val('');
        }
    }else {
        //busca os dados do destinatário
        $.ajax({
            type: "GET",
            url: '/empresa/empresa/get/id/'+id_empresa_transportador,
            success: function(data){
                if(type == 'CNPJ'){
                    $('#empresa_transportador').val(data.nome_razao);
                }else{
                    $('#cnpj_transportador').maskCnpj(data.cnpj_cpf);
                }
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
            
        });

    }

};

//mascara do cnpj/cpf
$.fn.maskCnpj = function(val){
    if(val == null){
        return;
    }
    if(val.length == 14){
        var primeiraParte = val.substr( 0, 2 );
        var segundaParte = val.substr( 2, 3 );
        var terceiraParte = val.substr( 5, 3 );
        var divisor = val.substr( 8, 4 );
        var identificador = val.substr( 12, 2 );

        $(this).val(primeiraParte+"."+segundaParte+"."+terceiraParte+"/"+divisor+"-"+identificador);
    }else if (val.length == 11){
        var primeiraParte = val.substr( 0, 3 );
        var segundaParte = val.substr( 3, 3 );
        var terceiraParte = val.substr( 6, 3 );
        var identificador = val.substr( 9, 2 );

        $(this).val(primeiraParte+"."+segundaParte+"."+terceiraParte+"-"+identificador);
    }
};

$.fn.gridItem = function(){
    var $this = $(this);
    var id_protocolo = $('#id_protocolo').val();
    
    $.ajax({
        type: "GET",
        url: "/material/protocolo/grid-item/id_protocolo/"+id_protocolo,
        success: function(data){
            $this.html(data);
        },
        error: function(){
            $this.html("Serviço temporariamente indisponivel. entre em contato com o administrador.");
        }
        
    });
};

$.fn.valueRadio = function(){
    var valor = null;
    $( this ).each(function() {
        //Verifica qual está selecionado
        if ($(this).is(':checked'))
            valor = parseInt($(this).val());
    });
    
    return valor;
};

$.fn.checked = function(){
    $(this).each(function(){ this.checked = true; });
};


$.fn.selectTransportador = function(){
    var id = $(this).val();
    if(id == ""){
        $('div.empresa_transportador').hide();
        $('div.funcionario_transportador').hide();
    }else if(id == 1){
        $('div.empresa_transportador').show('slow');
        $('div.funcionario_transportador').hide('slow');
    }else if(id == 2){
        $('div.empresa_transportador').hide('slow');
        $('div.funcionario_transportador').show('slow');
    }
    
    $('#id_tp_transportador').change(function(e){
        var id = $(this).val();
        if(id == ""){
            $('div.empresa_transportador').hide();
            $('div.funcionario_transportador').hide();
            $('#cnpj_transportador, #empresa_transportador, #id_transportador, #id_transp_empresa').val('');
            $('#id_funcionario_transportador').val('');
        }else if(id == 1){
            $('div.empresa_transportador').show('slow');
            $('div.funcionario_transportador').hide('slow');
            $('#id_funcionario_transportador').val('');
        }else if(id == 2){
            $('div.empresa_transportador').hide('slow');
            $('div.funcionario_transportador').show('slow');
            $('.empresa_transportador').val('');
        }
    });
};

/**
 * @todo terminar a função para alterar decimal
 * @param number
 */
function decimal(number){
    return number.toFixed(2).replace('.', ',');
}

$.fn.maskTelefone = function(){
    $(this).keyup(function(event) {
        var tel = $(this).unmaskedVal();
        if(tel[0] == 0 || tel[1] == 0){
            $(this).setMask("9999 999 999999");
        }else if(tel.length > 13){
            $(this).setMask("(99)99999-99999");
        }else{
            $(this).setMask("(99)9999-99999");
        }
    });
};