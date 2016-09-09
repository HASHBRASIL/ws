$(document).ready(function(){
    $('#observacao').limit('255','#countDescricao');
    $( "#dt_entrada" ).datepicker();
    $('#hr_entrada').timepicker({
        minuteStep: 1,
        showSeconds: true,
        showMeridian: false,
        defaultTime: false
    });
    $("#dt_entrada").setMask({mask:'99/99/9999',autoTab: false});
    $("#hr_entrada").setMask({mask:'99:99:99',autoTab: false});
    $("#cnpj_fornecedor, #cnpj_receptora").setMask({mask:'99.999.999/9999-99',autoTab: false});
    //unsetMask() PARA RETIRAR A MASCARA JQUERY
    
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
    $('body').on('click', 'img.add_controle_fornecedor', function(){
        $(this).parent().append('<span><input type="text" name="controle_fornecedor[]" value="" style="display: inline-block; width: 60%;">'+
                        '<img src="/images/delete.png" style="cursor:pointer;padding-left: 10px;" data-tooltip="" class="remove_controle_fornecedor" data-tooltip title="Remover controle do fornecedor"></span>');
    });
    //remove o campo de protocolo do fornecedor
    $('body').on('click', 'img.remove_controle_fornecedor', function(){
        $(this).parent().remove();
        $('.tooltip').hide();
    });
    
    //adicionando campo de protocolo do fornecedor
    $('body').on('click', 'img.add_controle_receptora', function(){
        $(this).parent().append('<span><input type="text" name="controle_receptora[]" value="" style="display: inline-block; width: 60%;">'+
                        '<img src="/images/delete.png" style="cursor:pointer;padding-left: 10px;" data-tooltip class="remove_controle_receptora" data-tooltip title="Adicionar controle do receptor"></span>');
    });
    //remove o campo de protocolo do fornecedor
    $('body').on('click', 'img.remove_controle_receptora', function(){
        $(this).parent().remove();
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
            }, dialogClass: 'ui-dialog-steelblue',
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
                    var nome = $('#nome_tp_entrada').val();
                    if(nome == ""){
                        alert('O campo nome é obrigatório.');
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: '/service/tipo-entrada/form',
                        data: {'nome' : $('#nome_tp_entrada').val()},
                        success: function(data){
                            if( data.success){
                                $.messageBox("Tipo de entrada adicionado com sucesso.", 'success');
                                $('#id_tp_entrada').append('<option value="'+data.id.id_tp_entrada+'" >'+nome+"</option>").val(data.id.id_tp_entrada);
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
            	}
              ],
              close: function(){
                  $('#nome_tp_entrada').val('');
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
            width: 360,
            height: 480,
            open: function(){
                $('#ope_telefone1').maskTelefone();
            },
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
              }],
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
                if(type == "CNPJ"){console.log(data.nome_razao);
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
