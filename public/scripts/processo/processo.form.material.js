$(document).ready(function(){
	$('.decimal').setMask();
	$('.grid-material').gridMaterial();

	    //abre o dialog para cadastrar material no processo
	    $("body").on("click", 'a.add-material',function(e){
	        e.preventDefault();
	        $('div.dialog-material').dialog({
	            modal: true,
	            dialogClass: 'ui-dialog-caribbean-green',
	            open: function(event, ui) {
	                $('#observacao_cliente').limit('300','#countObservMaterial');
	                $('.div_atributo').html('');
	                $('.decimal').setMask();
	                autocompleteMaterial();

	                $('.tipoMaterial[value="1"]').prop('checked', true).trigger('change');
	            },
	            title: "Material",
	            position: [($(window).width() / 2) - (500 / 2), 200],
	            width: "500",
	            height: "auto",
	            buttons:
                        [
        	                {
        	                    'text': 'Cancelar',
        	                    'class' : 'btn red',
            	                click: function() {
            	                    $( 'div.dialog-material' ).dialog( "close" );
            	                },
        	                },
        	                {
        	                      'text' : 'Adicionar',
        	                      'class' : 'btn green',
            	                  click: function() {
            	                      var item = $('#id_item').val();
            	                      if(item == "" && $('.tipoMaterial[value="1"]').is(':checked')){
            	                          alert('Selecione um produto.');
            	                          return;
            	                      }
            	                      if($('#quantidade_material').val() == "0,00"){
            	                          alert("selecione uma quantidade.");
            	                          return;
            	                      }
                                      if($('#id_unidade').val() == ""){
                                          alert("selecione uma unidade.");
                                          return;
                                      }
            	                      var dataPost;
            	                      if($('.tipoMaterial[value="1"]').is(':checked')){
            	                          dataPost = {id_material_processo:$('#id_material_processo').val(),
            	                                  id_processo: $('#pro_id').val(),id_item:$('#id_item').val(),
                                                  id_tipo_unidade:$('#id_unidade').val(), id_marca:$('#id_marca').val(),
                                                  quantidade: $('#quantidade_material').val(), observacao: $('#observacao_cliente').val(),
                                                  vl_unitario: $('#vl_unitario_material').val(), total:$('#total_material').val(),
                                                  id_tp_material:$('input.tipoMaterial').filter(':checked').val()};
            	                      }else if($('.tipoMaterial[value="2"]').is(':checked')){
            	                          dataPost = {id_processo: $('#pro_id').val(), nome:$('#nome_item').val(),
                                                  id_tipo_unidade:$('#id_unidade').val(), id_marca:$('#id_marca').val(),
                                                  quantidade: $('#quantidade_material').val(), observacao: $('#observacao_cliente').val(),
                                                  vl_unitario: $('#vl_unitario_material').val(), total:$('#total_material').val(),
                                                  id_tp_material:$('input.tipoMaterial').filter(':checked').val()};
            	                      }
                                      $.ajax({
                                          type: "POST",
                                          url: 'processo/material-processo/form?'+$('.select-atributo').serialize(),
                                          data: dataPost,
                                          success: function(data){
                                              if( data.success){
                                                  alert("Item Cadastrado.");
                                                  $( 'div.dialog-material' ).dialog( "close" );
                                                  $('.grid-material').gridMaterial();
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
	                  $('#id_material_processo').val('');
	                  $("#nome_item").val("");
	                  $("#id_item").val("");
	                  $("#id_unidade").val("");
	                  $("#id_marca").val("");
	                  $("#qtd_estoque").val("");
	                  $("#quantidade_material").val("");
                      $("#vl_unitario_material").val("");
                      $("#total_material").val("");
	                  $('#observacao_cliente').val('');
	                  $('.tipoMaterial').removeAttr('disabled');
	              }
	        });

	    });

	    //abre a arvore de produtos
	    $('body').on('click', 'a.add_item', function(e){
	        e.preventDefault();
	        $.ajax({
	            type: "GET",
	            url: 'material/item/tree',
	            success: function(data){
	                $('div#dialog_add_item > div').html(data);
	                $('div#dialog_add_item').dialog({
	                    dialogClass: 'ui-dialog-caribbean-green',
                        modal: true,
	                    position: [($(window).width() / 2) - (400 / 2), 150],
	                    title: "Produto",
	                    width: 450,
	                    height: 'auto',
	                });
	            }
	        });
	    });

	    //calcula a quantidade com o valor unitário
	    $('body').on('change', '#quantidade_material, #vl_unitario_material', function(){
            var vl_unitario_material = parseFloat($('#vl_unitario_material').val().replace(/\./g, '').replace(',', '.'));
            var quantidade_material  = parseFloat( $('#quantidade_material').val().replace(/\./g, '').replace(',', '.'));
            $('#total_material').val( decimal(vl_unitario_material * quantidade_material));
            $('#total_material').setMask('decimal');
        });

	    //seleciona o item da arvore
	    $('body').on('click', 'li.item', function(){
	        var text = $(this).text();
	        var id_item = $(this).attr('id-item');
	        var id_unidade_consumo = $(this).attr('id_unidade_consumo');

	        $('#nome_item').val(text.trim());
	        $('#id_item').val(id_item);
	        $('#id_unidade').val( id_unidade_consumo);
	        $('div#dialog_add_item').dialog('close');

	    });

	    $('body').on('change', '.tipoMaterial', function(){
	        $('.tipoMaterial').uniform();
	        var value = $(this).val();
	        if(value == 1){
                $('div.saida').show('slow');
                $('#id_unidade').attr('disabled', true);
                $('#nome_item').removeClass('span12').addClass('span10');
                $('a.add_item').show('slow');
                $( "#nome_item" ).autocomplete( "enable" );

	        }else{
                $('#id_unidade').removeAttr('disabled');
                $('a.add_item').hide('slow',function(){
                    $('#nome_item').removeClass('span10').addClass('span12');
                });
                $( "#nome_item" ).autocomplete( "disable" );
	        }
	        $('#nome_item').val('');
	        $('#id_item').val('');
            $('#id_unidade').val('');
            $('#id_marca').val('');
	    });
	    //ao selecionar outra marca busca o tipo de material
	    $('body').on('change', '#id_marca', function(){
	        var id_marca = $(this).val();
	        var id_item  = $('#id_item').val();
            // traz a quantidade existente no estoque
            $.getJSON("material/estoque/sum-estoque/idItem/"+id_item+"/idMarca/"+id_marca,function(data){
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

	    //havendo mudanças na quantidade do lote irá mudar o total solicitado
	    $('body').on('change', 'input.qtd_prot_solicitada', function(e){
	        var total = 0;
	        var qtd_solicitada   = $(this).val();
	        $(this).parent().parent().parent().parent().find('input.qtd_prot_solicitada');

	        var qtd_estoque      = $(this).parent().parent().parent().parent().find('input.qtd_protocolo').val();
	        qtd_solicitada       = parseFloat($(this).val().replace('.', '').replace(',', '.'));
	        if(qtd_estoque){
	            qtd_estoque          = parseFloat(qtd_estoque.replace('.', '').replace(',', '.'));
	        }else{
	            qtd_estoque          = 0;
	        }
	        qtd_solicitada       = parseFloat(qtd_solicitada.replace? qtd_solicitada.replace('.', '').replace(',', '.') : qtd_solicitada);
	        if(qtd_solicitada > qtd_estoque){
	            alert("A quantidade solicitada e maior do que a quantidade que se encontra no estoque.");
	            $(this).val($(this).parent().parent().parent().parent().find('input.qtd_protocolo').val());
	        }
	        $('input.qtd_prot_solicitada').each(function (i) {
	            total = parseFloat(total)+parseFloat($(this).val().replace('.', '').replace(',', '.'));
	        });
	        total = decimal(total);
	        $('input.total_qtd_solicitado').val(total).setMask('decimal');
	    });

	    $('body').on('click', '.validar_material', function(e){
	        e.preventDefault();
	        var id_material_processo = $(this).attr('id_material_processo');

	        $.ajax({
                type: "POST",
                url: 'processo/material-processo/get',
                data: {id: id_material_processo},
                success: function(data){
                    selectAtributo(data.id_item);
                	//abre o dialog
        	        $('div.dialog-material').dialog({
        	            modal: true,
        	            dialogClass: 'ui-dialog-caribbean-green',
        	            open: function(event, ui) {
        	                $('.div_atributo').html('');
        	                $('#observacao_cliente').limit('300','#countObservMaterial');

        	                $('.decimal').setMask();
        	                autocompleteMaterial();

        	                $('.tipoMaterial[value="1"]').prop('checked', true).trigger('change');
        	            },
        	            title: "Material",
        	            position: [($(window).width() / 2) - (500 / 2), 200],
        	            width: "500",
        	            height: "auto",
        	            buttons: [
        	                      {
    	                            'text': 'Cancelar material',
    	                            'class': 'btn red',
                	                click: function() {
                	                	var observacao_cliente = $('#observacao_cliente').val();
              	                      var dataPost = {id_material_processo:$('#id_material_processo').val(),
          	                                  id_processo: $('#pro_id').val(),id_item:$('#id_item').val(),
                                                id_tipo_unidade:$('#id_unidade').val(), id_marca:$('#id_marca').val(),
                                                quantidade: $('#quantidade_material').val(), observacao: $('#observacao_cliente').val(),
                                                vl_unitario: $('#vl_unitario_material').val(), total:$('#total_material').val(),
                                                id_tp_material:$('input.tipoMaterial').filter(':checked').val(), id_status_material:5};
              	                      if(observacao_cliente == ""){
              	                    	  alert('A observação é obrigatório.');
              	                    	  return;
              	                      }
                                        $.ajax({
                                            type: "POST",
                                            url: 'processo/material-processo/validar',
                                            data: dataPost,
                                            success: function(data){
                                                if( data.success){
                                                    alert("Material cancelado com sucesso.");
                                                    $( 'div.dialog-material' ).dialog( "close" );
                                                    $('.grid-material').gridMaterial();
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
        	                      },
        	                      {
        	                          'text': 'Validar material',
        	                          'class': 'btn green',
                	                  click: function() {
                	                      var item = $('#id_item').val();
                	                      if(item == "" && $('.tipoMaterial[value="1"]').is(':checked')){
                	                          alert('Selecione um produto.');
                	                          return;
                	                      }
                	                      if($('#quantidade_material').val() == "0,00"){
                	                          alert("selecione uma quantidade.");
                	                          return;
                	                      }
                                          if($('#id_unidade').val() == ""){
                                              alert("selecione uma unidade.");
                                              return;
                                          }
                	                      var dataPost = {id_material_processo:$('#id_material_processo').val(),
            	                                  id_processo: $('#pro_id').val(),id_item:$('#id_item').val(),
                                                  id_tipo_unidade:$('#id_unidade').val(), id_marca:$('#id_marca').val(),
                                                  quantidade: $('#quantidade_material').val(), observacao: $('#observacao_cliente').val(),
                                                  vl_unitario: $('#vl_unitario_material').val(), total:$('#total_material').val(),
                                                  id_tp_material:$('input.tipoMaterial').filter(':checked').val()};
                                          $.ajax({
                                              type: "POST",
                                              url: 'processo/material-processo/validar?'+$('.select-atributo').serialize(),
                                              data: dataPost,
                                              success: function(data){
                                                  if( data.success){
                                                      alert("Material validado com sucesso.");
                                                      $( 'div.dialog-material' ).dialog( "close" );
                                                      $('.grid-material').gridMaterial();
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
        	                  $('#id_material_processo').val('');
        	                  $("#nome_item").val("");
        	                  $("#id_item").val("");
        	                  $("#id_unidade").val("");
        	                  $("#id_marca").val("");
        	                  $("#qtd_estoque").val("");
        	                  $("#quantidade_material").val("");
                              $("#vl_unitario_material").val("");
                              $("#total_material").val("");
        	                  $('#observacao_cliente').val('');
        	                  $('.tipoMaterial').removeAttr('disabled');
        	              }
        	        });
                	//endDialog
                    $('#id_material_processo').val(id_material_processo);
                    $("#nome_item").val(data.nome_item);
                    $("#id_item").val(data.id_item);
                    $("#id_unidade").val(data.id_tipo_unidade);
                    $("#id_marca").val(data.id_marca);
                    $("#quantidade_material").val(data.quantidade.replace('.', ','));
                    $("#vl_unitario_material").val(data.vl_unitario.replace('.', ','));
                    $("#total_material").val(data.total.replace('.', ','));
                    $('#observacao_cliente').val(data.observacao);

                    if(data.atributo.length != 0){
                        setTimeout(function(){
                            $.each(data.atributo, function( index, value){
                                $('.select-atributo option[value="'+value.id_opcao+'"]').parent('select').val(value.id_opcao);
                            });
                        },1000);
                    }
                    $('.tipoMaterial').attr('disabled', true);
                    $('.decimal').setMask('decimal');

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
	    });

       $('body').on('click', '.history_material', function(e){
            e.preventDefault();
            var id_material_processo = $(this).attr('id_material_processo');

            $.ajax({
                type: "POST",
                url: 'processo/historico-material/grid/id_material_processo',
                data: {id_material_processo: id_material_processo},
                success: function(data){
                    $('div#grid_historico_material').html(data);
                    $('div#grid_historico_material').dialog({
                        modal: true,
                        resizable: true,
                        dialogClass: 'ui-dialog-caribbean-green',
                        position: [($(window).width() / 2) - (1100 / 2), 150],
                        title: "Historico de material",
                        width: 1100,
                        height: 'auto',
                    });
                },
                error: function(){
                    alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                }
            });
        });


       $('body').on('click', '.ver_lote_material', function(e){
            e.preventDefault();
            var id_material_processo = $(this).attr('id_material_processo');

            $.ajax({
                type: "POST",
                url: 'material/movimento/grid-lote-processo',
                data: {id_material_processo: id_material_processo},
                success: function(data){
                    $('div#grid_historico_material').html(data);
                    $('div#grid_historico_material').dialog({
                        dialogClass: 'ui-dialog-caribbean-green',
                        modal: true,
                        resizable: true,
                        position: [($(window).width() / 2) - (1100 / 2), 150],
                        title: "Baixa de material",
                        width: 1100,
                        height: 'auto',
                    });
                },
                error: function(){
                    alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                }
            });
        });

	    $('body').on('click', '.baixar_material', function(e){
	        e.preventDefault();
	        var id_material_processo = $(this).attr('id_material_processo');

            $.ajax({
                type: "POST",
                url: 'processo/material-processo/get',
                data: {id: id_material_processo},
                success: function(data){
                    dataAtributo = serializeAtributo(data.atributo);
                    $.ajax({
                        type: "POST",
                        url: 'material/estoque/sum-estoque/idItem/'+data.id_item+'/id_workspace/'+$('#id_workspace').val()+'/id/'+id_material_processo,
                        data: dataAtributo,
                        success: function(sum){
                            if(sum.qtd_estoque == null){
                                alert("Não contém este material no estoque.");
                                return
                            }
                            qtd_solicitada = parseFloat(data.quantidade) - parseFloat(data.qtd_baixado == null ? "0":data.qtd_baixado);
                            if(parseFloat(sum.qtd_estoque) < qtd_solicitada){
                                qtd_solicitada = parseFloat(sum.qtd_estoque);
                            }
                            $(this).salvarMovimentoSaida(data.id_item, qtd_solicitada, sum.qtd_estoque, id_material_processo, dataAtributo);
                        },
                        error: function(){
                            alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                        }
                    });
                },
                error: function(){
                    alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                }
            });

	    });

	    $('body').on('click', '.cancelar_material', function(e){
	        e.preventDefault();
	        var id_material_processo = $(this).attr('id_material_processo');

	      //abre o dialog
	        $('div.dialog_cancelar_material').dialog({
	            modal: true,
                title: "Cancelar material",
                position: [($(window).width() / 2) - (350 / 2), 200],
                width: "350",
                height: "auto",
                dialogClass: "ui-dialog-caribbean-green",
	            open: function(event, ui) {
	            	$('#observacao_cancelar_material').limit('300','#countObservCancelarMaterial');
	            },
	            buttons:[
	            {
	                'text': 'Cancelar material',
	                'class': 'btn red',
	                'click': function() {
	                	var observacao_cancelar_material = $('#observacao_cancelar_material').val();
	                      var dataPost = {id_material_processo:id_material_processo, observacao:observacao_cancelar_material,id_status_material:5};
	                      if(observacao_cancelar_material == ""){
	                          alert('A observação é obrigatório.');
	                          return;
	                      }
                        $.ajax({
                            type: "POST",
                            url: 'processo/material-processo/validar',
                            data: dataPost,
                            success: function(data){
                                if( data.success){
                                    alert("Material cancelado com sucesso.");
                                    $( 'div.dialog_cancelar_material' ).dialog( "close" );
                                    $('.grid-material').gridMaterial();
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
	                  },
	                }
	            ],
	              close: function(){
	                  $('#observacao_cancelar_material').val('');
	              }
	        });
        	//endDialog
	    });


	    //deletar um lote de baixa
	    $('body').on('click', 'i.del-lote-baixa', function(){
	        $(this).parent().parent().parent().parent().remove();
	    });//deketar um lote de baixa

	    //deletar um lote de baixa
        $('body').on('click', 'i.add-lote-baixa', function(){
            var html = '<div class="row-fluid">'+
                        '<div class="span4">'+
                        '<div class="control-group">'+
                        '<label class="control-label">Cod. do Lote 1</label>'+
                        '<div class="controls">'+
                        '<input type="hidden" name="id_estoque[]" class="num_protocolo" value="">'+
                        '<input type="text" name="num_protocolo[]" class="span12 num_protocolo " value="" autocomplete="off">'+
                        '</div></div></div>'+
                        '<div class="span4"><div class="control-group">'+
                        '<label class="control-label">Qtd em estoque</label>'+
                        '<div class="controls">'+
                        '<input type="text" disabled="disabled" name="qtd_protocolo[]" class="span12 qtd_protocolo " value="" style="text-align: right;">'+
                        '</div></div></div>'+
                        '<div class="span4"><div class="control-group">'+
                        '<label class="control-label">Qtd solicitado</label>'+
                        '<div class="controls">'+
                        '<input type="text" name="qtd_prot_solicitada[]" class="span10 qtd_prot_solicitada " value="0,00" style="text-align: right;"><i class="icon-remove del-lote-baixa"></i>'+
                        '</div></div></div></div>';
            $('form.form_baixa .row-fluid').eq(-2).after(html);
            $('.qtd_prot_solicitada').setMask('decimal');
        });//deketar um lote de baixa

});

$.fn.salvarMovimentoSaida = function(id_item, qtd_solicitada, qtd_estoque, id_material_processo, arrayAtributo){
    $('.decimal').setMask();
    var total_pedido    = qtd_solicitada;
    $('#id_item').val(id_item);

    if(qtd_estoque < qtd_solicitada){
        qtd_solicitada = qtd_estoque;
    }
    $('div.remover_lote').dialog({
        modal: true,
        resizable: true,
        position: [($(window).width() / 2) - (500 / 2), 150],
        dialogClass: 'ui-dialog-caribbean-green',
        title: "Lotes",

        width: 450,
        height: 'auto',
        maxHeight: 600,
        open: function(){
            $.getJSON('material/estoque/get-lote/qtd_solicitada/'+qtd_solicitada+"/id_item/"+id_item+'/id_workspace/'+$('#id_workspace').val()+'?'+arrayAtributo,function(data){
                $( 'form.form_baixa' ).html("");
                if(data == ""){
                    alert('Não existe lote para este produto.');
                    $('div.remover_lote').dialog('close');
                    return;
                }
                $.each(data, function(key, estoque){
                    if(key != "total"){
                        var indice = parseInt(key)+1;
                        var quantidade = estoque.quantidade.replace('.', ',');
                        var html = "<input type='hidden' class='array_atributo' value='"+arrayAtributo+"'>";
                        html += "<div class='row-fluid'>";
                        html += "<div class='span4'>";
                        html += "<div class='control-group'>";
                        html += "<label class='control-label'>Cod. do Lote "+indice+"</label>";
                        html += "<div  class='controls'> ";
                        html += "<input type='hidden' name='id_estoque[]' class='num_protocolo' value='"+estoque.id_estoque+"' >";
                        html += "<input type='text' name='num_protocolo["+estoque.id_estoque+"]' class='span12 num_protocolo' value='"+estoque.cod_lote+"' >";
                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                        html += "<div class='span4'>";
                        html += "<div class='control-group'>";
                        html += "<label class='control-label'>Qtd em estoque</label>";
                        html += "<div  class='controls'> ";
                        html += "<input type='text' disabled='disabled' name='qtd_protocolo[]' class='span12 qtd_protocolo id_estoque_"+estoque.id_estoque+"' value='"+quantidade+"' >";
                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                        html += "<div class='span4'>";
                        if(qtd_solicitada == 0){
                            quantidade = 0;
                        }else if(parseFloat(qtd_solicitada) < estoque.quantidade){
                            //estoque.quantidade = qtd_solicitada - estoque.quantidade;
                            quantidade = decimal(qtd_solicitada);
                            qtd_solicitada = 0;
                        }else{
                            qtd_solicitada = qtd_solicitada - estoque.quantidade;
                        }
                        html += "<div class='control-group'>";
                        html += "<label  class='control-label'>Qtd solicitado</label>";
                        html += "<div  class='controls'>";
                        html += "<input type='text' name='qtd_prot_solicitada["+estoque.id_estoque+"]' class='span10 qtd_prot_solicitada id_estoque_"+estoque.id_estoque+"' value='"+quantidade+"' >";
                        if(key == 0){
                            html += "<i class='icon-plus add-lote-baixa'></i>";
                        }else{
                            html += "<i class='icon-remove del-lote-baixa'></i>";
                        }

                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                        $(html).appendTo( 'form.form_baixa' );
                    }
                });
                var totalHtml = "<div class='row-fluid'>";
                totalHtml += "<div class='span4'>";
                totalHtml += "<div class='control-group'>";
                totalHtml += "<label class='crontrol-label'>Total Pedido</label>";
                totalHtml += "<div  class='controls'>";
                totalHtml += "<input style='text-align:right;' type='text' name='total_qtd_pedido' class='span12 total_qtd_pedido' disabled='disabled' value='"+total_pedido+"' >";
                totalHtml += "</div>";
                totalHtml += "</div>";
                totalHtml += "</div>";
                totalHtml += "<div class='span4 offset4'>";
                totalHtml += "<div class='control-group'>";
                totalHtml += "<label class='crontrol-label'>Total Solicitado</label>";
                totalHtml += "<div  class='controls'>";
                totalHtml += "<input style='text-align:right;' type='text' name='total_qtd_solicitado' class='span10 total_qtd_solicitado' disabled='disabled' value='"+data.total.replace('.', ',')+"' >";
                totalHtml += "<input style='text-align:right;' type='hidden'  name='total_qtd_protocolo' class='total_qtd_estoque' disabled='disabled' value='"+data.total.replace('.', ',')+"' >";
                totalHtml += "</div>";
                totalHtml += "</div>";
                totalHtml += "</div>";
                $(totalHtml).appendTo('form.form_baixa');
                $('.total_qtd_protocolo, .qtd_protocolo, .qtd_prot_solicitada').setMask('decimal');
                $('input.qtd_prot_solicitada').trigger('change');
            });

        },
        buttons: [
                  {
                      'class': 'btn red',
                      'text': 'Cancelar',
                      "click": function() {
                          $( 'div.remover_lote' ).dialog( "close" );
                      }
                  },
                  {
                        'class': 'btn green',
                        'text': 'Adicionar',
                        "click": function() {
                            var total_qtd_pedido        = $('.total_qtd_pedido').val();
                            var total_qtd_solicitado    = $('.total_qtd_solicitado').val();
                            var total_qtd_estoque       = $('.total_qtd_estoque').val();
                            var id_processo             = $('#pro_id').val();

                            total_qtd_pedido            = parseFloat(total_qtd_pedido.replace('.','').replace(',', '.'));
                            total_qtd_solicitado        = parseFloat(total_qtd_solicitado.replace('.','').replace(',', '.'));
                            total_qtd_estoque         = parseFloat(total_qtd_estoque.replace('.','').replace(',', '.'));

                            if(total_qtd_pedido < total_qtd_solicitado){
                                alert("A quantidade solicitada não está correta!");
                                return;
                            }
                            if(total_qtd_solicitado > total_qtd_estoque){
                                alert("A quantidade solicitada e maior do que a quantidade que se encontra no estoque.");
                                return;
                            }

                            $.ajax({
                                type: "POST",
                                url: 'material/movimento/form',
                                data: $('form.form_item').serialize()+'&'+$('form.form_baixa').serialize()+
                                '&id_processo='+id_processo+'&id_tp_movimento=2'+
                                '&total_qtd_solicitado='+$('.total_qtd_solicitado').val()+
                                '&id_material_processo='+id_material_processo,
                                success: function(data){
                                    if( data.success){
                                        alert("Baixa realizado com sucesso.");
                                        $( 'div.remover_lote' ).dialog( "close" );
                                        $('.grid-material').gridMaterial();
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
              $('#id_item').val('');
          }
    });


    $('body').on('focus', ".num_protocolo", function(){
        var id_item         = $('#id_item').val();
      //autocomplete do protocolo
        $( ".num_protocolo" ).autocomplete({
            source: "material/estoque/autocomplete-lote/id_item/"+id_item+'/id_workspace/'+$('#id_workspace').val()+'?'+$('.array_atributo').val(),
            select: function( event, ui ) {
                var existLote = false;
                $('.num_protocolo').each(function( index ) {
                    if(ui.item.id == $(this).siblings("input[type='hidden']").val()){
                        existLote = true;
                    }
                });
                if(existLote == false){
                    $(this).attr('name', 'num_protocolo['+ui.item.id+']');
                    $(this).siblings("input[type='hidden']").val(ui.item.id);
                    $(this).parent().parent().parent().parent().find('input.qtd_prot_solicitada').attr('name', 'qtd_prot_solicitada['+ui.item.id+']');
                    $(this).parent().parent().parent().parent().find('input.qtd_protocolo').val(ui.item.quantidade);
                    $('.qtd_protocolo, .qtd_prot_solicitada').setMask('decimal');
                    $('input.qtd_prot_solicitada').trigger('change');
                    $('input.qtd_protocolo').trigger('change');
                }else{
                    alert("O lote selecionado já existe por favor selecione um lote válido.");
                }
            },
            search: function( event, ui ) {
                $(this).val();
                $(this).siblings("input[type='hidden']").val('');
                $(this).parent().parent().parent().parent().find('input.qtd_prot_solicitada').attr('name', 'qtd_prot_solicitada[]').val('0,00');
                $(this).parent().parent().parent().parent().find('input.qtd_protocolo').val('');
            }
        });
    });
};


/**
 * @todo terminar a função para alterar decimal
 * @param number
 */
function decimal(number){
    if(number == null){
        return;
    }
    if(number.toFixed){
        return number.toFixed(2).replace('.', ',');
    }else{
        number = parseFloat(number);
        return number.toFixed(2).replace('.', ',');
    }

}

$.fn.gridMaterial = function(){
    var id_processo= $('#pro_id').val();
    var isCompany = $('#isCompany').val();
    var $this = $(this);
    /* se quem estiver logado for uma empresa não irá mostrar a grid */
    if(id_processo == ''){
        return;
    }
    $.ajax({
        type: "GET",
        url: "processo/material-processo/grid/pro_id/"+id_processo,
        beforeSend: function(){
            $("#load").show();
        },
        success: function(data){
            $this.html(data);
        },
        complete: function(){
            $("#load").hide();
        },
        error: function(){
            alert("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err1");
        }

    });
};

/**
 * @desc autocomplete do material dentro do dialog
 */
function autocompleteMaterial(){
    $( "#nome_item" ).autocomplete({
        source: "material/item/autocomplete-estoque",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_item').val(ui.item.id);
            $('#id_unidade').val(ui.item.id_tipo_unidade_consumo);
            selectAtributo(ui.item.id);

        },
        search: function( event, ui ) {
            $('#id_item').val("");
            $('#id_unidade').val("");
            $("#qtd_estoque").val("");
            $('div.div-atributo').html('');
        }
      });
}

function selectAtributo(id_item){
    if(id_item == ""){
        $('div.div-atributo').html('');
        return;
    }

    $.get('material/item-opcao/get-by-item/id_item/'+id_item, function(data){
        if(data.count == 0){
            $('div.div-atributo').html('');
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

function serializeAtributo(atributo){
    var url = '';
    if(atributo.length != 0){
        $.each(atributo, function( index, value){
            if(index > 0){
                url += '&';
            }
            url += 'id_opcao['+index+']='+value.id_opcao;
        });
    }
    return url;
}
