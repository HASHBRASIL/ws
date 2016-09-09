$(document).ready(function(){
    $('.decimal').setMask();
    $('#cep').setMask({'mask':'99999-999', autoTab: false});
    $('#numero').setMask({'mask':'999999', autoTab: false});
    $('#telefone, #celular, #fax').maskTelefone();
    $('#grid_entrega').grid();
    $('#observacao').limit('255','#countObservacao');
    $('body').on('click', 'button.incluir', function(){
        var valid = true;
        var validMaior = false;
        
        if($('.error').length > 0){
            alert('Existe quantidade solicitada acima da quantidade que existe no estoque');
            return;
        }
        $.each( $('input.decimal'), function( key, value ) {
            var id_item = $(this).attr('id-item');
            var quantidade = $('#quantidade_'+id_item).val();
            var qtd_estoque = $('#qtd_estoque_'+id_item).val();
            quantidade = quantidade.replace('.', '').replace(',', '.');
            qtd_estoque = qtd_estoque.replace('.', '').replace(',', '.');
            if( parseFloat(quantidade) > parseFloat(qtd_estoque) ){
                $('#quantidade_'+id_item).focus();
                validMaior = true;
                return;
            }
            
            if(value.value != ""){
                valid = false;
                return;
            }
        });
        
        if(validMaior){
            alert('Existe quantidade solicitada acima da quantidade que existe no estoque');
            return;
        }
        if(valid){
            alert('Prencha a quantidade desejada.');
            return;
        }
        
        $('div.dialog_endereco').dialog({
            modal: true,
            resizable: true,
            dialogClass: 'ui-dialog-purple',
            position: [($(window).width() / 2) - (450 / 2), 200],
            title: "Adicionar Produto",
            width: 450,
            height: 570,
            open: function(){
            },
            buttons: [
                      {
                        'text': 'Cancelar',
                        'class': 'btn red',
                        "click": function() {
                          $( 'div.dialog_endereco' ).dialog( "close" );
                        },
                      },
                      {
                        'text': "Adicionar",
                        'class':'btn green',
                        "click": function() {
                            if($('#cep').val() == ""){
                                $('#cep').attr('class', 'error');
                                alert('O cep é um campo obrigatório');
                                return false;
                            }
                            if($('#logradouro').val() == ""){
                                $('#logradouro').attr('class', 'error');
                                alert('O logradouro é um campo obrigatório');
                                return false;
                            }
                            if($('#numero').val() == ""){
                                $('#numero').attr('class', 'error');
                                alert('O número é um campo obrigatório');
                                return false;
                            }
                            if($('#id_cidade').val() == "Cidade"){
                                alert('Selecione a cidade');
                                return false;
                            }
                            
                            $.ajax({
                                type: "post",
                                url: '/material/entrega/form',
                                data: $('form.geral').serialize()+"&"+$('.form_endereco').serialize(),
                                success: function(data){
                                    if(data.success){
                                        $('div.dialog_endereco').dialog('close');
                                        location.href = "/material/pedido/index";
                                    }else{
                                        $.messageBox(data.mensagem[0].text, data.mensagem[0].type);
                                    }
                                },
                                error: function(data){
                                    alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                                }
                            });
                        }
                      }
                     ],
              close: function(){
                  $('#destinatario').val('');
                  $('#aos_cuidados').val('');
                  $('#email').val('');
                  $('#cep').val('');
                  $('#logradouro').val('');
                  $('#numero').val('');
                  $('#complemento').val('');
                  $('#bairro').val('');
                  $('#ufs_id').val('');
                  $('#id_cidade').val('');
                  $('#telefone').val('');
                  $('#fax').val('');
                  $('#celular').val('');
              }
        });
    });
    
    $('body').on('change', '.error', function(){
        if($(this).val() != ""){
            $(this).removeAttr('class');
        }
    });
    
    $('body').on('click', '.error', function(){
        if($(this).val() != ""){
            $(this).removeAttr('class');
        }
    });
    
    $('body').on('click', '#bt_pesquisar_cep', function(){
        $(this).findCep();
    });
    
    $('body').on('change', '#ufs_id', function(){
        $(this).selectCidade(null);
    });
    
    $('body').on('change', '.decimal', function(){
        var id_item = $(this).attr('id-item');
        var quantidade = $('#quantidade_'+id_item).val();
        var qtd_estoque = $('#qtd_estoque_'+id_item).val();
        quantidade = quantidade.replace('.', '').replace(',', '.');
        qtd_estoque = qtd_estoque.replace('.', '').replace(',', '.');
        if( parseFloat(quantidade) > parseFloat(qtd_estoque) ){
            alert('Existe quantidade solicitada acima da quantidade que existe no estoque');
            $('#quantidade_'+id_item).focus();
        }
        
    });
    
    $('body').on('submit', 'form.geral', function(){
        return false;
    });
    
    $('body').on('click', 'a.view_extrato', function(e){
        e.preventDefault();
        var id_item = $(this).attr('href');

        $('div#dialog_extrato').dialog({
            modal: true,
            resizable: true,
            position: [($(window).width() / 2) - (450 / 2), 200],
            title: "Extrato simples",
            width: 450,
            dialogClass: 'ui-dialog-purple',
            height: 'auto',
            open: function(){
                $.ajax({
                    type: "GET",
                    url: '/material/estoque/extrato/id_item/'+id_item,
                    success: function(data){
                        $('#dialog_extrato').html(data);
                    },
                    complete: function(){
                    }
                });
            },
              close: function(){
              }
        });
    });
});
//mascara do telefone se tiver mais que um local
$.fn.maskTelefone = function(){
    $(this).setMask({'mask':'(99)9999-99999', autoTab: false});
    // se tiver mais do que 12 caracteres o campo ele irá mudar a mascara para telefone com mais um digito
    $(this).keyup(function(event) {
        var cpf = $(this).unmaskedVal();
        if(cpf.length > 13){
            $(this).setMask({'mask':'(99)99999-9999', autoTab: false});
        }else{
            $(this).setMask({'mask':'(99)9999-99999', autoTab: false});
        }
    });
};

//busca o cep e preenche os campos
$.fn.findCep = function(){
    if($.trim($("#cep").val()) != ""){
        $.getScript("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+$("#cep").val(), function(){
            if(resultadoCEP["resultado"] == 1){
                //$("#tipo_logradouro").val(unescape(resultadoCEP["tipo_logradouro"]));
                $("#logradouro").val(unescape(resultadoCEP["logradouro"]));
                $("#bairro").val(unescape(resultadoCEP["bairro"]));
                $('#ufs_id > option').each(function(){
                    if($(this).text() == unescape(resultadoCEP["uf"])){
                        var value = $(this).attr('value');
                        $('#ufs_id').val(value);
                        $(this).selectCidade(unescape(resultadoCEP["cidade"]));
                    } 
                });
                //$("#city").val(unescape(resultadoCEP["cidade"]));
                //$("#state").val(unescape(resultadoCEP["uf"]));
                $("#numero").focus();
            }else{
                alert("Endereço não encontrado para o cep ");
            }
        });
    }
};

//carrega o combo da cidade conforme o uf selecionado
$.fn.selectCidade = function (txt_cidade){
    var id_estado = $('#ufs_id').val();
    if(id_estado == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: '/sis/cidade/pairs/id_estado/'+id_estado,
        success: function(data){
            var list = data.list;
            var html =  "<option >Cidade</option>";
            $.each(list, function(key, value){
                html += "<option value='"+key+"'>"+value+"</option>";
            });
            $('#id_cidade').html(html);
        },
        complete: function(){
            if(txt_cidade != null){
                $('#id_cidade > option').each(function(){
                    if($(this).text() == txt_cidade){
                        var CidadeVal = $(this).attr('value');
                        $('#id_cidade').val(CidadeVal);
                    }
                });
            }
        }
    });
};

//carrega a grid de endereço
$.fn.grid = function (){
    var $this = $(this);
    $.ajax({
        type: "GET",
        url: '/material/entrega/grid',
        success: function(data){
            $this.html(data);
            $('.decimal').setMask();
        },
        error: function(data){
            alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
        }
    });
};
