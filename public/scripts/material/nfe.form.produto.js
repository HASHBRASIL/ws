$(document).ready(function(){
    $('#table_item').gridItem();
    
    // adicionar produto no protocolo
    $('body').on('click', 'a.novo-produto', function(e){
        e.preventDefault();
        var id_nfe    = $('#id_nfe').val();
        if(id_nfe == ""){
            alert("Preencha os campos obrigatórios e salva para cadastrar um produto.");
            return;
        }

        $('div.saida').hide();
        $('div.entrada').show();

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
            title: "Entrada produto",
            width: 500,
            dialogClass: 'ui-dialog-purple',
            height: 'auto',
            open: function(){
                
                $('.decimal').setMask();
                $( "#nome_item" ).autocomplete({
                    source: "/material/item/autocomplete",
                    minLength: 2,
                    select: function( event, ui ) {
                        $('#id_item').val(ui.item.id);
                        selectAtributo(ui.item.id);
                        $('#id_tipo_unidade').val(ui.item.id_tipo_unidade_compra);
                        $('#id_unidade').val(ui.item.id_tipo_unidade_compra);
                    },
                    search: function( event, ui ) {
                        $('#id_item').val("");
                        $('#id_tipo_unidade').val("");
                        $('#id_unidade').val("");
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
                             "text" : "Lote manual",
                            click: function() {
                                var item = $('#id_item').val();
                                if(item == ""){
                                    alert('Selecione um produto.');
                                    return;
                                }
                                if($('div.dialog_item input.quantidade').val() == "0,00"){
                                    alert("selecione uma quantidade.");
                                    return;
                                }
                                $('#lote_manual').val(1);
                                $(this).entradaMaterial(item);
                                
                            }
                          },
                         {
                            'class' : 'btn green',
                            "text" : "Lote automático",
                             click: function() {
                                 var item = $('#id_item').val();
                                 if(item == ""){
                                     alert('Selecione um produto.');
                                     return;
                                 }
                                 if($('div.dialog_item input.quantidade').val() == "0,00"){
                                     alert("selecione uma quantidade.");
                                     return;
                                 }
                                 $('#lote_manual').val(0);
                                 $(this).entradaMaterial(item);
                             }
                   }
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
        var id_tp_movimento = 1;
        var id_unidade_compra = $(this).attr('id_unidade_compra');
        var id_unidade_consumo = $(this).attr('id_unidade_consumo');
        
        $('#nome_item').val(text.trim());
        $('#id_item').val(id_item);
        $('#id_unidade').val(id_tp_movimento == 1 ? id_unidade_compra: id_unidade_consumo);
        $('#id_tipo_unidade').val(id_tp_movimento == 1 ? id_unidade_compra: id_unidade_consumo);
        $('div#dialog_add_item').dialog('close');
        
    });
    
    //calcula o total do item
    $('body').on('blur', 'div.dialog_item input.quantidade', function(){
        if($('#vl_unitario').val() != '0,00'){
            var quantidade  = $(this).val().replace(".","").replace(",",".");
            var vl_unitario = $('#vl_unitario').val().replace(".","").replace(",",".");
            var total       = quantidade*vl_unitario;
            $('#vl_total').val(parseFloat(total, 10).toFixed(2).replace(".",","));
            $('#vl_total').setMask();
        }else{
            var quantidade = $(this).val().replace(".","").replace(",",".");
            var vl_total    = $('#vl_total').val().replace(".","").replace(",",".");
            var vl_unitario = vl_total/quantidade;
            $('#vl_unitario').val(parseFloat(vl_unitario, 10).toFixed(2).replace(".",","));
            $('#vl_unitario').setMask();
        }
    });
    
    //calcula o total do item
    $('body').on('blur', '#vl_unitario', function(){
        if($('div.dialog_item input.quantidade').val() != '0,00'){
            var vl_unitario     = $(this).val().replace(".","").replace(",",".");
            var quantidade      = $('div.dialog_item input.quantidade').val().replace(".","").replace(",",".");
            var total           = quantidade*vl_unitario;
            $('#vl_total').val(parseFloat(total, 10).toFixed(2).replace(".",","));
            $('#vl_total').setMask();
        }else{
            var vl_unitario     = $(this).val().replace(".","").replace(",",".");
            var vl_total        = $('#vl_total').val().replace(".","").replace(",",".");
            var quantidade      = vl_total/vl_unitario;
            $('.quantidade').val(parseFloat(quantidade, 10).toFixed(2).replace(".",","));
            $('.quantidade').setMask();
        }
    });
    
    //calcula o total do item
    $('body').on('blur', '#vl_total', function(){
        if($('#vl_unitario').val() == '0,00'){
            var quantidade     = $('.quantidade').val().replace(".","").replace(",",".");
            var vl_total       = $('#vl_total').val().replace(".","").replace(",",".");
            var vl_unitario    = vl_total/quantidade;
            $('#vl_unitario').val(parseFloat(vl_unitario, 10).toFixed(2).replace(".",","));
            $('#vl_unitario').setMask();
        }else if($('.quantidade').val() == '0,00'){
            var vl_unitario     = $('#vl_unitario').val().replace(".","").replace(",",".");
            var vl_total        = $('#vl_total').val().replace(".","").replace(",",".");
            var quantidade      = vl_total/vl_unitario;
            $('.quantidade').val(parseFloat(quantidade, 10).toFixed(2).replace(".",","));
            $('.quantidade').setMask();
        }
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
            dialogClass: 'ui-dialog-purple',
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

});
//começando as functions
$.fn.gridItem = function(){
    var $this = $(this);
    var id_nfe = $('#id_nfe').val();
    
    $.ajax({
        type: "GET",
        url: "/material/nfe/grid-item/id_nfe/"+id_nfe,
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

// salva no movimento a entrada de material manual de produto no estoque
$.fn.entradaMaterial = function(id_item, id_estoque){
    if($('#id_item').val() == ""){
        $('#id_item').val(id_item);
    }

    $('#id_estoque').val("");
    $('#quantidade_unidade').val('');
    $('div#dialog-adiciona-lote').dialog({
        modal: true,
        resizable: true,
        position: [($(window).width() / 2) - (400 / 2), 150],
        title: "Adicionar lote",
        width: 400,
        dialogClass: 'ui-dialog-purple',
        height: 'auto',
        open: function(){
            $('div.dialog_item').dialog('close');
            $.ajax({
                type: "GET",
                url: '/material/item/get/id_item/'+id_item,
                success: function(data){
                    if( data.success ){
                        $('span.unidade_compra').text(data.nome_unidade_compra);
                        $('span.unidade_consumo').text(data.nome_unidade_consumo);

                        $('input.unidade_compra').val(data.id_tipo_unidade_compra).attr('unidade', data.nome_unidade_compra);
                        $('input.unidade_consumo').val(data.id_tipo_unidade_consumo).attr('unidade', data.nome_unidade_consumo);
                        
                        if(id_estoque != null){
                            $('#id_estoque').val(id_estoque);
                        }
                        $('label.qtd_unidade').html("quantidade de "+data.nome_unidade_consumo.toLowerCase()+" por "+data.nome_unidade_compra.toLowerCase()+'<span class="required">*</span>');
                        
                        if(data.id_unidade_rastreabilidade != null){
                            $('.rastreabilidade[value="'+data.id_unidade_rastreabilidade+'"]').checked();
                            $('.rastreabilidade').attr('disabled', 'disabled');
                            $('.rastreabilidade').uniform();
                        }else{
                            $('.rastreabilidade').removeAttr('disabled');
                            $('.rastreabilidade').removeAttr('checked');
                            $('.rastreabilidade').uniform();
                        }
                    }else{
                        alert(data.mensagem[0].text);
                        $( 'div#dialog-adiciona-lote' ).dialog( "close" );
                    }
                },
                error: function(){
                    alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                    $( 'div#dialog-adiciona-lote' ).dialog( "close" );
                }
            });
        },
        buttons:
            [
             {
                 'class' : 'btn red',
                 "text" : "Cancelar",
                click: function() {
                    $(this).dialog('close');
                }
              },
              {
                  'class' : 'btn green',
                  "text" : "Gerar lote",
                 click: function() {
                     var qtd_unidade = $('#quantidade_unidade').val();
                     if(qtd_unidade == "0,00" || qtd_unidade == ""){
                         alert('preencha o campo quantidade por unidade.');
                         return;
                     }
                     if($('.rastreabilidade').is(':checked') == false){
                         alert('selecione uma rastreabilidade.');
                         return;
                     }

                     var qtd_lote = 0;
                     var qtd_unidade;
                     if($('.unidade_compra').is(':checked')){
                         qtd_lote = parseFloat( $('div.dialog_item input.quantidade').val().replace('.', '').replace(',', '.'));
                         qtd_unidade = $('#quantidade_unidade').val();
                     }else{
                         var quantidade = parseFloat( $('div.dialog_item input.quantidade').val().replace('.', '').replace(',', '.'));
                         var quantidade_unidade = parseFloat($('#quantidade_unidade').val().replace('.', '').replace(',', '.'));
                         qtd_lote = quantidade * quantidade_unidade;
                         qtd_unidade = '1,00';
                     }
                     //manual
                     if($('#lote_manual').val() == 1){
                         var html = "<form id='form-lote-manual' style='margin:0'>";
                         for(var i = 1; i <= qtd_lote; i++){
                             html += "<div class='row-fluid'>"+
                                     "<div class='span6'>"+
                                     '<div class="control-group">'+
                                     '<label class="control-label">Cód. lote</label>'+
                                     '<div class="controls">'+
                                     '<input type="text" name="cod_lote[]" class="cod_lote span9" />'+
                                     '<input type="hidden" value="" class="id_estoque" alt="integer" />'+
                                     '</div>'+
                                     '</div>'+
                                     '</div>'+
                                     "<div class='span6'>"+
                                     '<div class="control-group">'+
                                     '<label class="control-label">Quantidade</label>'+
                                     '<div class="controls">'+
                                     '<input type="text" value="'+qtd_unidade+'" class="quantidade_unidade_lote span12" disabled="disabled" />'+
                                     '</div>'+
                                     '</div>'+
                                     '</div>'+
                                     '</div>';
                         }
                         html += "</form>";
                         $('#dialog-lote').append(html);
                         $('div#dialog-lote').dialog('open');
                         $('div#dialog-adiciona-lote').dialog('close');
                         $('input.cod_lote').setMask('integer');
                     }else{
                         $('div#dialog-adiciona-lote').dialog('close');
                         $('div#dialog-lote-automatico').dialog({
                             modal: true,
                             resizable: true,
                             position: [($(window).width() / 2) - (300 / 2), 150],
                             title: "Lote automatico",
                             width: 300,
                             maxHeight: 500,
                             dialogClass: 'ui-dialog-purple',
                             closeOnEscape: false,
                             height: 'auto',
                               close: function(){
                                   $('.ui-dialog-titlebar button.ui-dialog-titlebar-close').show();
                               },
                               open: function(){
                                   $('.ui-dialog-titlebar button.ui-dialog-titlebar-close').hide();
                                   $("#porcentage-creation-lote").knob();
                                   $('#porcentage-creation-lote')
                                   .val(0)
                                   .trigger('change');
                                   $('button.disabled-lote-automatico').hide();
                                   $.post('/material/movimento/form', {id_movimento:$('#id_movimento').val(),
                                                                       id_nfe:$('#id_nfe').val(),
                                                                       id_processo: $('#id_processo').val(),
                                                                       quantidade: $('.quantidade').val(),
                                                                       rastreabilidade: $('.rastreabilidade:checked').val(),
                                                                       id_item: $('#id_item').val(),
                                                                       id_tp_movimento: 1},
                                                                       function(data){
                                                                           if(data.success){
                                                                               loteautomatico(data.id.id_movimento,qtd_unidade, qtd_lote, 1 );
                                                                           }else{
                                                                               alert('Não foi possivel gerar o lote tente novamente mais tarde.');
                                                                               $('div#dialog-lote-automatico').dialog('close');
                                                                           }
                                                                       }
                                   );
                               },
                               buttons:[
                                       {
                                           'text':'Ok',
                                           'class':'btn green disabled-lote-automatico',
                                           click: function(){
                                               $(this).dialog('close');
                                           }
                                       }
                                       ]
                         });
                     }
                 }
               },
           ],
          close: function(){
          }
    });
}

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
    });
    
    
}

$('div#dialog-lote').dialog({
    modal: true,
    resizable: true,
    position: [($(window).width() / 2) - (300 / 2), 150],
    title: "Adicionar lote",
    width: 300,
    maxHeight: 500,
    dialogClass: 'ui-dialog-purple dialog-lote',
    autoOpen: false,
    closeOnEscape: false,
    height: 'auto',
      close: function(){
          $('div#dialog-lote').html("<input type='hidden' name='id_movimento' id='id_movimento' >");
          $('.dialog-lote div.ui-dialog-titlebar button.ui-dialog-titlebar-close').show();
      },
      open: function(){
          $('.dialog-lote div.ui-dialog-titlebar button.ui-dialog-titlebar-close').hide();
          $.post('/material/movimento/form', {id_movimento:$('#id_movimento').val(),
                                              id_nfe:$('#id_nfe').val(),
                                              quantidade: $('div.dialog_item input.quantidade').val(),
                                              rastreabilidade: $('.rastreabilidade:checked').val(),
                                              id_item: $('#id_item').val(),
                                              id_tp_movimento: 1},
                                              function(data){
                                                  if(data.success){
                                                      $('#id_movimento').val(data.id.id_movimento);
                                                  }else{
                                                      alert('Não foi possivel gerar o lote tente novamente mais tarde.');
                                                      $('div#dialog-lote').dialog('close');
                                                  }
                                              }
          );
      },
      buttons:[
              {
                  'text':'Ok',
                  'class':'btn green',
                  click: function(){
                      if($('form#form-lote-manual').find('i.icon-remove').length == 0){
                          $(this).dialog('close');
                          $('#table_item').gridItem();
                      }else{
                          alert('Possui lote com erro.');
                          $('form#form-lote-manual').find('i.icon-remove').focus();
                      }
                      
                  }
              }
              ]
});

function loteautomatico(id_movimento,qtd_unidade, qtd_max_loop, loop){
    $.post('/material/estoque/form', $('form.form_item').serialize()+'&quantidade_unidade_lote='+qtd_unidade+
            '&id_movimento='+id_movimento+'&id_workspace='+$('#id_workspace').val(),
                function(data){
                    if(data.success){
                        var porcentagem = (loop*100)/qtd_max_loop;
                        $('#porcentage-creation-lote')
                        .val(porcentagem.toFixed(2))
                        .trigger('change');
                        $('div#dialog-lote-automatico').dialog('option', 'title', 'Lote automático - '+loop+' de '+qtd_max_loop);
                        if(qtd_max_loop > loop){
                            loop = loop+1;
                            loteautomatico(id_movimento,qtd_unidade, qtd_max_loop, loop);
                        }else{
                            $('button.disabled-lote-automatico').show('slow');
                            $('#table_item').gridItem();
                        }
                    }else{
                        loteautomatico(id_movimento,qtd_unidade, qtd_max_loop, loop);
                    }
                }
    );
}
