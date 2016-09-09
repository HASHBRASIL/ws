$(document).ready(function(){
    FormWizard.init();
    $( "#dt_emissao" ).datepicker({
            onClose: function( selectedDate ) {
                $( "#dt_saida" ).datepicker( "option", "minDate", selectedDate );
              }
    });
    $( "#dt_saida" ).datepicker({
        onClose: function( selectedDate ) {
            $( "#dt_entrada" ).datepicker( "option", "minDate", selectedDate );
          }
    });
    $( " #dt_entrada" ).datepicker();
    $("#dt_saida, #dt_emissao, #dt_entrada").setMask({mask:'99/99/9999',autoTab: false});
    $("#hr_saida, #hr_entrada").setMask({mask:'99:99:99',autoTab: false});
    
    $('.decimal').setMask();
    $("#num_danfe, #num_serie, #ncm_sh, #cst, #cfop").setMask({mask:'9', type:'repeat'});
    
    // autocomplete do fornecedor
    $( "#empresa_fornecedor" ).autocomplete({
        source: "/empresa/empresa/fornecedor-json",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_fornecedor').val(ui.item.id);
            $('#id_fornecedor').camposFornecedor();
        },
        search: function( event, ui ) {
            $('#id_fornecedor').val("");
            $('#id_fornecedor').camposFornecedor();
        }
      });
    
    $( "#empresa_destinatario" ).autocomplete({
        source: "/empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_empresa_destinatario').val(ui.item.id);
            $('#id_endereco_destinatario').camposDestinatario();
        },
        search: function( event, ui ) {
            $('#id_empresa_destinatario').val("");
            $('#id_endereco_destinatario').camposDestinatario();
        }
      });
    
    $( "#razao_transportador" ).autocomplete({
        source: "/empresa/empresa/transportador-json",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_transp_empresa').val(ui.item.id);
            $('#id_transp_empresa').camposTransportador();
        },
        search: function( event, ui ) {
            $('#id_transp_empresa').val("");
            $('#id_transp_empresa').camposTransportador();
        }
      });

    $('.salvar_nfe').click(function(){
        $("#form_nfe").salvarNfe();
    });
    
    $(".form_nfe").submit(function(){
        var $total = $("#tl_produto");
        if($total.val() != $('#total_produto_grid').val()){
            var validar = confirm('Os valores total dos itens não é igual ao valor total da nota. Gostaria de salvar mesmo assim?');
            if(!validar){
                return false;
            }
        }
    });
    
    // irá inativar um item
    $('body').on('click', 'a.delete_item', function(e){
        e.preventDefault();
        var id = $(this).attr('href');
        var resultado = confirm('Deseja realmente excluir este dado?');
        if(resultado == false){
            return;
        }
        
        $.ajax({
            type: "GET",
            url: '/material/estoque/delete/id/'+id,
            success: function(data){
                if(data[0].type == 'success'){
                    $.messageBox("Dado excluido com sucesso.", 'success');
                    $('#table_item').gridItem();
                }else{
                    $.messageBox("Não foi possivel excluir o dado.", 'error');
                }
                $('#tl_produto').verificarTotal();
            },
            error: function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });

    
    $('#tl_produto').blur(function(){
        $(this).verificarTotal();
    });
    
});

$.fn.camposFornecedor = function(){
    var id_fornecedor =  $('#id_fornecedor').val();
    
    if(id_fornecedor == ""){
        $('.campo_fornecedor').val('');
    }else {
        //busca os dados do fornecedor
        $.ajax({
            type: "GET",
            url: '/empresa/empresa/get/id/'+id_fornecedor,
            success: function(data){
                $('#razao_fornecedor').val(data.nome_razao);
                $('#cnpj_fornecedor').maskCnpj(data.cnpj_cpf);
                $('#estadual_fornecedor').val(data.estadual);
                $('#fone_fornecedor').maskTelefone(data.telefone1);
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
            
        });

        //busca o endereco
        $(this).getEndereco('/sis/endereco/get-by-empresa/id_empresa/'+id_fornecedor, 'fornecedor');
    }

};

$.fn.checked = function(){
    $(this).each(function(){ this.checked = true; });
};

//campo Remetente
$.fn.camposDestinatario = function(){
    var id_destinatario =  $('#id_empresa_destinatario').val();
    
    if(id_destinatario == ""){
        $('.campo_destinatario').val('');
    }else {
        //busca os dados do destinatário
        $.ajax({
            type: "GET",
            url: '/empresa/empresa/get/id/'+id_destinatario,
            success: function(data){
                $('#razao_destinatario').val(data.nome_razao);
                $('#cnpj_destinatario').maskCnpj(data.cnpj_cpf);
                $('#estadual_destinatario').val(data.estadual);
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
            
        });

        //busca o endereco
        $(this).getEndereco('/sis/endereco/get-by-empresa/id_empresa/'+id_destinatario, 'destinatario');
        
    }

};


//campos transportadores
$.fn.camposTransportador = function(){
    var id_transp_empresa =  $('#id_transp_empresa').val();
    
    if(id_transp_empresa == ""){
        $('.campo_transportador').val('');
    }else {
        //busca os dados do fornecedor
        $.ajax({
            type: "GET",
            url: '/empresa/empresa/get/id/'+id_transp_empresa,
            success: function(data){
                $('#cnpj_transportador').maskCnpj(data.cnpj_cpf);
                $('#estadual_transportador').val(data.estadual);
                $('#fone_transportador').maskTelefone(data.telefone1);
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
            
        });

        //busca o endereco
        $(this).getEndereco('/sis/endereco/get-by-empresa/id_empresa/'+id_transp_empresa, 'transportador');
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

//mascara do telefone
$.fn.maskTelefone = function(val){
    if(val == null){
        return;
    }
    if(val.length == 10){
        var ddd = val.substr( 0, 2 );
        var primeiraParte = val.substr( 2, 4 );
        var segundaParte = val.substr( 6, 4 );

        $(this).val("("+ddd+")"+primeiraParte+"-"+segundaParte);
        
    }else if (val.length == 11){
        var ddd = val.substr( 0, 2 );
        var primeiraParte = val.substr( 2, 5 );
        var segundaParte = val.substr( 7, 4 );

        $(this).val("("+ddd+")"+primeiraParte+"-"+segundaParte);
        
    }else if (val.length == 12){
        var codigo = val.substr( 0, 2 );
        var ddd = val.substr( 2, 2 );
        var primeiraParte = val.substr( 4, 4 );
        var segundaParte = val.substr( 8, 4 );

        $(this).val("+"+codigo+"("+ddd+")"+primeiraParte+"-"+segundaParte);
        
    }else if (val.length == 13){
        var codigo = val.substr( 0, 2 );
        var ddd = val.substr( 2, 2 );
        var primeiraParte = val.substr( 5, 4 );
        var segundaParte = val.substr( 9, 4 );

        $(this).val("+"+codigo+"("+ddd+")"+primeiraParte+"-"+segundaParte);
    }
};
//mascara do cep
$.fn.maskCep = function(val){
    if(val == null){
        return;
    }
    var primeiraParte = val.substr(0, 5);
    var segundaParte = val.substr(5, 3);
    
    $(this).val(primeiraParte+"-"+segundaParte);
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

$.fn.getEndereco = function(url, campo){
    $.ajax({
        type: "GET",
        url: url,
        success: function(data){
            if(data.length > 1){
                var html = "";
                $.each(data, function(key, value){
                    var tipo_endereco = value.tipo_endereco ? value.tipo_endereco : "Não possui Tipo";
                    html += "<tr>";
                    html += "<td><input type='radio' name='escolha_endereco' class='escolha_endereco' value='"+key+"'></td><td><strong>"+tipo_endereco +"</strong><br>"+value.nome_logradouro+" - "+value.bairro+" <br>"+value.cidade_nome+"/"+value.uf_sigla+"</td>";
                    html += "</tr>";
                });
                $('.table_endereco').html(html);
                $('.dialog_endereco').dialog({
                    modal: true,
                    resizable: true,
                    dialogClass: 'ui-dialog-purple',
                    title: "Escolher Endereço",
                    position: [($(window).width() / 2) - (450 / 2), 200],
                    width: 450,
                    height: 'auto',
                    buttons: [
                              {
                                  'text': 'Adicionar',
                                  'class': 'btn green',
                                "click": function() {
                                    var value = $('.escolha_endereco').valueRadio();
                                    if(value != null){
                                        var cidade = data[value].cidade_nome ? data[value].cidade_nome : "";
                                        var estado = data[value].uf_sigla ? "/"+data[value].uf_sigla : "";
                                        $('#endereco_'+campo).val(data[value].tipo_logradouro+" "+data[value].nome_logradouro);
                                        $('#bairro_'+campo).val(data[value].bairro);
                                        $('#cidade_'+campo).val(cidade+estado);
                                        $('#cep_'+campo).maskCep(data[value].cep);
                                        if(campo == "destinatario"){
                                            $('#id_endereco_destinatario').val(data[value].id);
                                        }else{
                                            $('#id_endereco_'+campo).val(data[value].id);
                                        }
                                        $( this ).dialog( "close" );
                                    }
                                }
                              }
                             ],
                      beforeClose: function(){
                          var value = $('.escolha_endereco').valueRadio();
                          if(value == null){
                              alert("Escolha um endereço.");
                              return false;
                          }
                      }
                });
            }else if(data.length == 1){
                var cidade = data[0].cidade_nome ? data[0].cidade_nome : "";
                var estado = data[0].uf_sigla ? "/"+data[0].uf_sigla : "";
                $('#endereco_'+campo).val(data[0].tipo_logradouro+" "+data[0].nome_logradouro);
                $('#bairro_'+campo).val(data[0].bairro);
                $('#cidade_'+campo).val(cidade+estado);
                $('#cep_'+campo).maskCep(data[0].cep);
                if(campo == "destinatario"){
                    $('#id_endereco_destinatario').val(data[0].id);
                }else{
                    $('#id_endereco_'+campo).val(data[0].id);
                }
            }
        },
        error: function(){
            alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
        }
        
    });
};

$.fn.salvarNfe = function(){
    if($('#natureza_operacao').val() == ""){
        $.messageBox("O campo natureza da operação está vazia.", 'alert');
        return;
    }
    $.ajax({
        type: "POST",
        url: "/material/nfe/form",
        data: $( this ).serialize(),
        success: function(data){
            if(data.success){
                if($("#id_nfe").val() == ""){
                    $("#id_nfe").val(data.id_nfe);
                    $.messageBox("Dado inserido com sucesso.", 'success');
                }else{
                    $.messageBox("Dado atualizado com sucesso.", 'success');
                }
                $("#id_imposto").val(data.id_imposto);
                $("#id_transportador").val(data.id_transportador);
            }else{
                $.messageBox(data.mensagem[0].text, data.mensagem[0].type);
            }
            
        },
        error: function(){
            alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
        }
        
    });
};

$.fn.verificarTotal = function(){
    var $this = $(this);
    if($this.val() == $('#total_produto_grid').val()){
        $this.addClass('success');
        $this.removeClass('error');
    }else{
        $this.addClass('error');
        $this.removeClass('success');
    }
};


var FormWizard = function () {
    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            var form = $('#form_nfe');
            var error = $('.alert-error', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    natureza_operacao: {
                        required: true,
                    },
                    num_danfe: {
                        required: true,
                    },
                    num_serie: {
                        required: true,
                    },
                    dt_emissao:{
                        required: true,
                    }
                },
                messages: { // custom messages for radio buttons and checkboxes
                    natureza_operacao: {
                        required: "Campo obrigatório."
                    },
                    num_danfe: {
                        required: "Campo obrigatório.",
                    },
                    num_serie:{
                        required: "Campo obrigatório.",
                    },
                    dt_emissao:{
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
                    App.scrollTo($('#form_nfe'));
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

                    App.scrollTo($('#form_nfe'));
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
                    App.scrollTo($('#form_nfe'));
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

                    App.scrollTo($('#form_nfe'));
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
        }

    };

}();