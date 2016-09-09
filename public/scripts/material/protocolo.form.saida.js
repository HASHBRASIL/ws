$(document).ready(function(){
    
    // adicionar produto no protocolo
    $('body').on('click', 'a.novo-produto', function(e){
        e.preventDefault();
        var id_protocolo    = $('#id_protocolo').val();
        if(id_protocolo == ""){
            alert("Preencha os campos obrigatórios e salva para cadastrar um produto.");
            return;
        }
        $('div.saida').show();
        $('div.entrada').hide();

        $('#id_estoque').val("");
        $('#codigo').val("");
        $('#id_item').val("");
        $('#nome_item').val("");
        $('#id_tipo_unidade').val("");
        $('#id_unidade').val("");
        $('.quantidade').val("");
        $('#vl_unitario').val("");
        $('#vl_total').val('');
        $('#id_marca').val('');
        $("#qtd_estoque").val('');
        $('div.div-atributo').html('');
        
        $('div.dialog_item').dialog({
            modal: true,
            resizable: true,
            position: [($(window).width() / 2) - (450 / 2), 200],
            title: "Baixar produto",
            width: 500,
            dialogClass: 'ui-dialog-purple',
            height: 'auto',
            open: function(){
                
                $('.decimal').setMask();
                $( "#nome_item" ).autocomplete({
                    source: "/material/item/autocomplete-estoque",
                    minLength: 2,
                    select: function( event, ui ) {
                        $('#id_item').val(ui.item.id);
                        selectAtributo(ui.item.id);
                        $('#id_tipo_unidade').val(ui.item.id_tipo_unidade_consumo);
                        $('#id_unidade').val(ui.item.id_tipo_unidade_consumo);
                    },
                    search: function( event, ui ) {
                        $('#id_item').val("");
                        $('#id_tipo_unidade').val("");
                        $('#id_unidade').val("");
                        $("#qtd_estoque").val("");
                        $('div.div-atributo').html('');
                    }
                  });
            },
            buttons: [
                        {
                            'class' : 'btn red',
                            "text" : "Cancelar",
                           click: function() {
                               $(this).dialog('close');
                           }
                         },
                         {
                             'class' : 'btn green',
                             "text" : "Baixar produto",
                            click: function() {
                                var item = $('#id_item').val();
                                if(item == ""){
                                    alert('Selecione um produto.');
                                    return;
                                }
                                if($('#quantidade').val() == "0,00"){
                                    alert("selecione uma quantidade.");
                                    return;
                                }
                                $(this).salvarMovimentoSaida(id_item);
                            }
                          },
                 ],
              close: function(){
              }
        });
        
    });
    
    //abre a arvore de produtos
    $('body').on('click', 'img.add_item', function(){
        $.ajax({
            type: "GET",
            url: '/material/item/tree',
            success: function(data){
                $('div#dialog_add_item').html(data);
                $('div#dialog_add_item').dialog({
                    modal: true,
                    resizable: true,
                    position: [($(window).width() / 2) - (400 / 2), 150],
                    title: "Produto",
                    width: 450,
                    height: 480,
                });
            }
        });
    });
    
    //seleciona o item da arvore
    $('body').on('click', 'li.item', function(){
        var text = $(this).text();
        var id_item = $(this).attr('id-item');
        var id_tp_movimento = $('#id_tp_movimento').val();
        var id_unidade_compra = $(this).attr('id_unidade_compra');
        var id_unidade_consumo = $(this).attr('id_unidade_consumo');
        
        $('#nome_item').val(text.trim());
        $('#id_item').val(id_item);
        $('#id_unidade').val(id_tp_movimento == 1 ? id_unidade_compra: id_unidade_consumo);
        $('#id_tipo_unidade').val(id_tp_movimento == 1 ? id_unidade_compra: id_unidade_consumo);
        $('div#dialog_add_item').dialog('close');
        
    });

    //havendo mudanças na quantidade do lote irá mudar o total solicitado
    $('body').on('change', 'input.qtd_prot_solicitada', function(e){
        var total = 0;
        var className        = $(this).attr('class');
        var qtd_solicitada   = $(this).val();
        className            = className.replace('qtd_prot_solicitada ', '').replace(' ', '');
        className            = className.replace('span12', '');
        var qtd_estoque      = $('.span12.qtd_protocolo.'+className).val();
        qtd_solicitada       = parseFloat($(this).val().replace('.', '').replace(',', '.'));
        if(qtd_estoque){
            qtd_estoque          = parseFloat(qtd_estoque.replace('.', '').replace(',', '.'));
        }else{
            qtd_estoque          = 0;
        }
        qtd_solicitada       = parseFloat(qtd_solicitada.replace? qtd_solicitada.replace('.', '').replace(',', '.') : qtd_solicitada);
        console.log('quantidade solicitada '+ qtd_solicitada);
        console.log('quantidade estoque '+ qtd_estoque);
        if(qtd_solicitada > qtd_estoque){
            alert("A quantidade solicitada e maior do que a quantidade que se encontra no estoque.");
            $(this).val($('.qtd_protocolo.'+className).val());
        }
        $('input.qtd_prot_solicitada').each(function (i) {
            
            total = parseFloat(total)+parseFloat($(this).val().replace('.', '').replace(',', '.'));
        });
        total = decimal(total);
        $('input.total_qtd_solicitado').val(total).setMask('decimal');
    });
    
    $('body').on('change', 'input.qtd_protocolo', function(e){
        var total = 0;
        $('input.qtd_protocolo').each(function (i) {
            total = parseFloat(total)+parseFloat($(this).val().replace('.', '').replace(',', '.'));
        });
        total = decimal(total);
        $('input.total_qtd_estoque').val(total).setMask('decimal');
    });
    
    
    $('body').on('focusout','input.cod_lote', function(){
        $(this).parent().find('i').remove();
        if($(this).val() == ""){
            $(this).parent().append('<i class="icon-remove" title="O campo código do lote não pode ser vazio"></i>');
            $(this).focus();
        }else{
            var $this = $(this);
            $.post('/material/estoque/form', $('form.form_item').serialize()+'&quantidade_unidade_lote='+$('.quantidade_unidade_lote').val()+
                                            '&cod_lote='+$this.val()+'&id_estoque='+$(this).parent().find('input.id_estoque').val()+
                                            '&id_movimento='+$('#id_movimento').val()+'&id_workspace='+$('#id_workspace').val(),
                                                function(data){
                                                    if(data.success){
                                                        $this.parent().find('i').remove();
                                                        $this.parent().find('input.id_estoque').val(data.id.id_estoque);
                                                        $this.parent().append('<i class="icon-ok check-form-success" title="Código inserido com sucesso"></i>');
                                                    }else{
                                                        $this.parent().find('i').remove();
                                                        $this.parent().append('<i class="icon-remove" title="'+data.mensagem[0].text+'" ></i>');
                                                        alert(data.mensagem[0].text);
                                                    }
                                                }
            );
        }
    });

    
    $('body').on('change', '.select-atributo', function(){
        // traz a quantidade existente no estoque
        var id_item = $('#id_item').val();
        $.post("/material/estoque/sum-estoque/idItem/"+id_item+'/id_workspace/'+$('#id_workspace').val(), $('.select-atributo').serialize(),function(data){
            if(data.qtd_estoque != null){
                $("#qtd_estoque").val(data.qtd_estoque.replace('.', ','));
                $('#qtd_estoque').setMask('decimal');
            }else{
                $("#qtd_estoque").val("não possui no estoque");
            }
        }).fail(function(){
            alert('Ocorreu um erro inesperado entre em contato com o administrador.');
        });
    });
});
//começando as functions
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

//Salva o movimento de saida do estoque
$.fn.salvarMovimentoSaida = function(id_item, id_estoque){
    $('.decimal').setMask();
    var qtd_solicitada  = $('#quantidade').val();
    var qtd_estoque     = $('#qtd_estoque').val();
    var id_item         = $('#id_item').val();
    var total_pedido    = $('#quantidade').val();
    
    qtd_solicitada      = parseFloat(qtd_solicitada.replace('.', '').replace(',', '.'));
    qtd_estoque          = parseFloat(qtd_estoque.replace('.', '').replace(',', '.'));
    if(qtd_estoque < qtd_solicitada || isNaN(qtd_estoque)){
        alert("A quantidade solicitada e maior do que a quantidade que se encontra no estoque.");
        return;
    }
    $('div.remover_lote').dialog({
        modal: true,
        resizable: true,
        position: [($(window).width() / 2) - (500 / 2), 150],
        title: "Lotes",
        width: 450,
        dialogClass: 'ui-dialog-purple dialog-lote',
        height: 'auto',
        maxHeight: 600,
        open: function(){
            $.post('/material/estoque/get-lote/qtd_solicitada/'+qtd_solicitada+"/id_item/"+id_item+"/id_workspace/"+$('#id_workspace').val(),$('.select-atributo').serialize(),function(data){
                $('form.form_baixa').html("");
                if(data == ""){
                    alert('Não existe lote para este produto.');
                    $('div.remover_lote').dialog('close');
                    return;
                }
                $.each(data, function(key, estoque){
                    if(key != "total"){
                        var indice = parseInt(key)+1;
                        var quantidade = estoque.quantidade.replace('.', ',');
                        var html = "<div class='row-fluid'>";
                        html += "<div class='span4'>";
                        html += "<div class='control-group'>";
                        html += "<label class='control-label'>Cod. do Lote "+indice+"</label>";
                        html += "<div class='controls'>";
                        html += "<input type='hidden' name='id_estoque[]' class='num_protocolo' value='"+estoque.id_estoque+"' >";
                        html += "<input type='text' name='num_protocolo["+estoque.id_estoque+"]' class='num_protocolo span12' value='"+estoque.cod_lote+"' >";
                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                        html += "<div class='span4'>";
                        html += "<div class='control-group'>";
                        html += "<label class='control-label'>Qtd em estoque</label>";
                        html += "<div class='controls'>";
                        html += "<input type='text' disabled='disabled' name='qtd_protocolo[]' class='qtd_protocolo span12 id_estoque_"+estoque.id_estoque+"' value='"+quantidade+"' >";
                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                        html += "<div class='span4'>";
                        html += "<div class='control-group'>";
                        if(qtd_solicitada == 0){
                            quantidade = 0;
                        }else if(qtd_solicitada < estoque.quantidade){
                            //estoque.quantidade = qtd_solicitada - estoque.quantidade;
                            quantidade = decimal(qtd_solicitada);
                            qtd_solicitada = 0;
                        }else{
                            qtd_solicitada = qtd_solicitada - estoque.quantidade;
                        }
                        html += "<label class='control-label'>Qtd solicitado</label>";
                        html += "<div class='controls'>";
                        html += "<input type='text' name='qtd_prot_solicitada["+estoque.id_estoque+"]' class='span12 qtd_prot_solicitada id_estoque_"+estoque.id_estoque+"' value='"+quantidade+"' >";
                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                        $(html).appendTo('form.form_baixa');
                    }
                });
                var totalHtml = "<div class='row-fluid'>";
                totalHtml += "<div class='span4'>";
                totalHtml += "<div class='control-group'>";
                totalHtml += "<label class='control-label'>Total Pedido</label>";
                totalHtml += "<div class='controls'>";
                totalHtml += "<input style='text-align:right;' type='text' name='total_qtd_pedido' class='total_qtd_pedido span12' disabled='disabled' value='"+total_pedido+"' >";
                totalHtml += "</div>";
                totalHtml += "</div>";
                totalHtml += "</div>";
                totalHtml += "<div class='span4 offset4'>";
                totalHtml += "<div class='control-group'>";
                totalHtml += "<label class='control-label'>Total Solicitado</label>";
                totalHtml += "<div class='controls'>";
                totalHtml += "<input style='text-align:right;' type='hidden'  name='total_qtd_protocolo' class='total_qtd_estoque' disabled='disabled' value='"+data.total.replace('.', ',')+"' >";
                totalHtml += "<input style='text-align:right;' type='text' name='total_qtd_solicitado' class='total_qtd_solicitado span12' disabled='disabled' value='"+data.total.replace('.', ',')+"' >";
                totalHtml += "</div>";
                totalHtml += "</div>";
                totalHtml += "</div>";
                $(totalHtml).appendTo('form.form_baixa');
                $('.total_qtd_protocolo, .qtd_protocolo, .qtd_prot_solicitada').setMask('decimal');
                $('input.qtd_prot_solicitada').trigger('change');
            });;
            
        },
        buttons: [
                   {
                       'class': 'btn red',
                       'text' : 'Cancelar',
                        "click": function() {
                          $( 'div.remover_lote' ).dialog( "close");
                        }
                   },
                   {
                       'class': 'btn green',
                       'text': 'Baixar',
                       "click": function() {
                           var total_qtd_pedido        = $('.total_qtd_pedido').val();
                           var total_qtd_solicitado    = $('.total_qtd_solicitado').val();
                           var total_qtd_estoque       = $('.total_qtd_estoque').val();
                           var id_protocolo            = $('#id_protocolo').val();
                           
                           total_qtd_pedido            = parseFloat(total_qtd_pedido.replace('.','').replace(',', '.'));
                           total_qtd_solicitado        = parseFloat(total_qtd_solicitado.replace('.','').replace(',', '.'));
                           total_qtd_estoque         = parseFloat(total_qtd_estoque.replace('.','').replace(',', '.'));
                           
                           if(total_qtd_pedido != total_qtd_solicitado){
                               alert("A quantidade solicitada não está correta!");
                               return;
                           }
                           if(total_qtd_solicitado > total_qtd_estoque){
                               alert("A quantidade solicitada e maior do que a quantidade que se encontra no estoque.");
                               return;
                           }
                           
                           $.ajax({
                               type: "POST",
                               url: '/material/movimento/form',
                               data: $('form.form_item').serialize()+'&'+$('form.form_baixa').serialize()+
                               '&id_protocolo='+id_protocolo+'&id_tp_movimento='+$('#id_tp_movimento').val()+
                               '&total_qtd_solicitado='+$('.total_qtd_solicitado').val(),
                               success: function(data){
                                   if( data.success){
                                       alert("Baixa realizado com sucesso.");
                                       $( 'div.remover_lote' ).dialog( "close" );
                                       $( 'div.dialog_item' ).dialog( "close" );
                                       $('#table_estoque').gridItem();
                                       $('img.add_tp_protocolo').remove();
                                       $('#id_tp_protocolo').attr('disabled', true);
                                       $('#id_processo').attr('disabled', true);
                                   }else{
                                       alert(data.mensagem[0].text);
                                   }
                               },
                               beforeSend: function(){
                                   $('.ui-dialog-buttonset').hide();
                               },
                               complete: function(){
                                   $('.ui-dialog-buttonset').show();
                               },
                               error: function(){
                                   alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                               }
                           });
                           
                       }
                       
                   }
          ],
          close: function(){
          }
    });
    

    $('body').on('focus', ".num_protocolo", function(){
        var id_item         = $('#id_item').val();
      //autocomplete do protocolo
        $( ".num_protocolo" ).autocomplete({
            source: "/material/estoque/autocomplete-lote/id_item/"+id_item+'/id_workspace/'+$('#id_workspace').val()+'?'+$('.select-atributo').serialize(),
            select: function( event, ui ) {
                var existLote = false;
                $('.num_protocolo').each(function( index ) {
                    if(ui.item.id == $(this).siblings("input[type='hidden']").val()){
                        existLote = true;
                    }
                });
                if(existLote == false){
                    var id_old = $(this).siblings("input[type='hidden']").val();
                    $(this).attr('name', 'num_protocolo['+ui.item.id+']');
                    $(this).siblings("input[type='hidden']").val(ui.item.id);
                    $('.qtd_prot_solicitada.id_estoque_'+id_old+'').val("0,00").addClass("id_estoque_"+ui.item.id+"").removeClass('id_estoque_'+id_old+'').attr('name', 'qtd_prot_solicitada['+ui.item.id+']');
                    $('.qtd_protocolo.id_estoque_'+id_old+'').val(ui.item.quantidade).addClass("id_estoque_"+ui.item.id+"").removeClass('id_estoque_'+id_old+'');
                    $('.qtd_protocolo, .qtd_prot_solicitada').setMask('decimal');
                    $('input.qtd_prot_solicitada').trigger('change');
                    $('input.qtd_protocolo').trigger('change');
                }else{
                    alert("O lote selecionado já existe por favor selecione um lote válido.");
                }
            },
            search: function( event, ui ) {
                $(this).val();
            }
        });
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

function selectAtributo(id_item){
    if(id_item == ""){
        return;
    }
    
    $.get('/material/item-opcao/get-by-item/id_item/'+id_item, function(data){
        if(data.count == 0){
            // traz a quantidade existente no estoque
            $.post("/material/estoque/sum-estoque/idItem/"+id_item+'/id_workspace/'+$('#id_workspace').val(),function(data){
                if(data.qtd_estoque != null){
                    $("#qtd_estoque").val(data.qtd_estoque.replace('.', ','));
                    $('#qtd_estoque').setMask('decimal');
                }else{
                    $("#qtd_estoque").val("não possui no estoque");
                }
            }).fail(function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            });
            return;
        }
        var id_atributo = 0;
        var html = "<div class='row-fluid'>";
        count_atributo = 0;
        $.each(data.list, function(index, value){
            if(count_atributo == 2){
                html += "</div>";
                html += "<div class='row-fluid'>";
                count_atributo = 0;
            }
            if(id_atributo != value.id_atributo){
                if(index > 0){
                    html += '</select>'+
                           '</div>'+
                           '</div>'+
                           '</div>';
                }
                html += '<div class="span6">'+
                        '<div class="control-group">'+
                            '<label class="control-label" style="text-transform:capitalize">'+value.nome_atributo+'<span class="required">*</span></label>'+
                            '<div class="controls">'+
                                '<select  name="id_opcao['+value.id_atributo+']" class="select-atributo">'+
                                    '<option value="'+value.id_opcao+'">'+value.nome_opcao+'</option>';
                id_atributo = value.id_atributo;
                count_atributo++;
            }else{
                html += '<option value="'+value.id_opcao+'">'+value.nome_opcao+'</option>';
            }
        });
        html += '</select>'+
        '</div>'+
        '</div>'+
        '</div>'+
        "</div>";
        $('div.div-atributo').html(html);
        $('div.div-atributo div.row-fluid .span6:nth-child(2n+3)' ).css('margin', 0);

        // traz a quantidade existente no estoque
        $.post("/material/estoque/sum-estoque/idItem/"+id_item+'/id_workspace/'+$('#id_workspace').val(), $('.select-atributo').serialize(),function(data){
            if(data.qtd_estoque != null){
                $("#qtd_estoque").val(data.qtd_estoque.replace('.', ','));
                $('#qtd_estoque').setMask('decimal');
            }else{
                $("#qtd_estoque").val("não possui no estoque");
            }
        }).fail(function(){
            alert('Ocorreu um erro inesperado entre em contato com o administrador.');
        });
    });
}