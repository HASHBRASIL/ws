$(document).ready(function(){
    $('#pro_desc_produto').limit('500','#countDescricao');
    $('#pro_prazo_entrega').limit('255','#countObserv');
	$("body").on("click", '.delete_item',function(event){

		var id = $(this).attr('value');

		$.ajax({
	        type: "POST",
	        url: "financial/agrupador-financeiro/delete/id_agrupador_financeiro/"+id,
	        data: { id: id},
	        success: function(data){

	        	 if(data[0].type == 'success'){
	                    $.messageBox("Dado excluido com sucesso.", 'success');
	                    $('#table_financial').gridItem();
	                }else{
	                    $.messageBox("Não foi possivel excluir o dado.", 'error');
	             }

	        	 $(".tooltip").hide();
	        }
	    });


	});

	$("body").on("click", '#vinculoFinancial',function(event){

		$('#agrupadorFinanceiroBox').dialog({
	        modal: true,
	        open: function(event, ui) {
	        },
	        title: "Gerar Agrupador Financeiro",
	        position: { my: "center", at: "top", of: window},
	        width: "500",
	        height: "370",
	        buttons: {
	            /*"Fechar": function() {
	              $( this ).dialog( "close" );

	            }*/
	          },
	          close: function(){

	          }
	    });

	});

	/*METODO PARA HABILITAR O AJAX DA PESQUISA RAPIDA QUANDO FOR PRECIONADO ENTER*/
	$("#quickSearch").keypress(function(e) {
		  if(e.which == 13) {
			  var id = $("#quickSearch").val();
				$.ajax({
			        type: "POST",
			        url: "processo/processo/quick-search-ajax/pro_id/"+id,
			        data: { id: id},
			        success: function(data){

			        	if (data.success == "true"){

			        	}else{
			        		alert("Código Não encontrado.");
			        	}
			        }
			    });
		  }
	});

	$("#quickSearchButton").click(function() {
		  var id = $("#quickSearch").val();
			$.ajax({
		        type: "POST",
		        url: "processo/processo/quick-search-ajax/pro_id/"+id,
		        data: { id: id},
		        success: function(data){

		        	if (data.success == "true"){
		        		 window.location.href="processo/processo/form/pro_id/"+data.processo;
		        	}else{
		        		alert("Código Não encontrado.");
		        	}
		        }
		    });
	});

	$('.decimal').setMask();

	$("#sta_id").click(function(){
		this.select();
	});

	$('#pro_data_entrega').datetimepicker({showSecond: true,timeFormat: 'HH:mm:ss'});

	$('.decimal').setMask();

	$('.numeral').setMask("9999999999999999999999999");


    $( "#empresa_sacado" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#empresas_id").val(ui.item.id);
        },
        search: function( event, ui ) {
        }
     });

    $( "#sta_id_selected" ).autocomplete({
        source: "processo/status/autocomplete",
        minLength: 1,
        select: function( event, ui ) {
        	$("#sta_id").val(ui.item.id);
        },
        search: function( event, ui ) {
        }
     });

    $.fn.gridItem = function(){
        var id_processo= $('#pro_id').val();
        var isCompany = $('#isCompany').val();
        var $this = $(this);
        /* se quem estiver logado for uma empresa não irá mostar a grid */
        if(id_processo == "" ){
            return;
        }

        $.ajax({
            type: "GET",
            url: "financial/agrupador-financeiro/grid-financial-processo-ajax/id_processo/"+id_processo,
            beforeSend: function(){
                $("#load").show();
            },
            success: function(data){
                $this.html(data);
            },
            complete: function(){
            	$("#load").hide();

                var financialCredit = $( '#financialCredit' ).val();
                var valorPedido = $('#pro_vlr_pedido').val();
                if(financialCredit == valorPedido){

                	$('#pro_vlr_pedido').removeClass("error");
                	$('#pro_vlr_pedido').addClass("success");

                }else{

                	$('#pro_vlr_pedido').removeClass("success");
                	$('#pro_vlr_pedido').addClass("error");

                }
            },
            error: function(){
            	alert("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err1");
            }

        });

    };

    if ($.isNumeric($('#pro_id').val())){

    	var id = $("#empresas_id").val();

    	//grid do limite financeiro do cliente deste processo
    	$.ajax({
            type: "POST",
            url: "financial/credito/limite-by-empresa-ajax/",
            data: { id: id},
            success: function(data){
            		$("#limiteByEmpresaAjax").html(data);
            }
        });

        if($('#pro_id').val() != "") {
            //Historico do processo
            $.ajax({
                type: "GET",
                url: "processo/historico/grid/pro_id/"+$('#pro_id').val()+'/limit/5',
                success: function(data){
                        $("#historicoProcesso").html(data);
                }
            });
        }

        //Mostra o tempo trabalhado
        $.ajax({
            type: "GET",
            url: "processo/pcp-timer/grid-processo/cod_pro/"+$('#pro_codigo_disable').val(),
            success: function(data){
                    $("#table_time").html(data);
            }
        });

        $('body').on('click', 'a.limit_historico', function(e){
            e.preventDefault(); $.ajax({
                type: "GET",
                url: "processo/historico/grid/pro_id/"+$('#pro_id').val(),
                success: function(data){
                        $("#historicoProcesso").html(data);
                }
            });
        });

    	if($('#isCompany').val() == 0){
            $("#vinculoFinancialBox").fadeIn("slow");
            //carrega a grid de financial
            $('#table_financial').gridItem();
            $('#table_material').gridMaterial();
            $('#table_servico').gridServico();

            //botão de adicionar material
            $("#add_material").fadeIn("slow");

            //botão de adicionar servico
            $("#add_servico").fadeIn("slow");
    	}

    }


	$('#stf_id').change(function(){

		if ($(this).val() == 1){
			$("#compensacaoBox").fadeIn();
		}else{
			$("#compensacaoBox").fadeOut();
			$("#fin_compensacao").datepicker({
        	    dateFormat: "dd-mm-yy"
        	}).datepicker("setDate", data);
		}
	});

	$('#salvar').click(function(){
		$("#form").submit();
	});

	$('#pre_impressao').change(function(){
	    var pre_impressao = $('#pre_impressao').is(':checked');
	    if(pre_impressao){
	        $('#pro_desc_produto').attr('disabled', 'disabled');
	        $('div.pre_impressao').append('<input type="hidden" name="pro_desc_produto" id="hidden_pro_desc_produto" >');
	        $('#dialog_descricao').dialog({
	            modal: true,
	            title: "Descrição de produção",
	            position: [($(window).width() / 2) - (450 / 2), 200],
	            width: "450",
	            height: "450",
	            buttons: {
	                "Adicionar": function() {
	                  var text = "";
	                  if($('#id_categoria').val() != ""){
	                      var id_categoria = $('#id_categoria').val();
	                      text += "Categoria: "+$('#id_categoria option[value="'+id_categoria+'"]').text()+"/";
	                  }
                      if($('#qtd_pagina').val() != ""){
                          text += "Qtd. de página(s): "+$('#qtd_pagina').val()+"/ ";
                      }
                      if($('#tam_pagina').val() != ""){
                          text += "Tamanho da página: "+$('#tam_pagina').val()+"/ ";
                      }
                      if($('#formato_pagina').val() != ""){
                          text += "Formato da Página: "+$('#formato_pagina').val()+"/ ";
                      }
                      if($('#cores').val() != ""){
                          text += "Formato da Cores: "+$('#cores').val()+"/ ";
                      }
                      if($('#id_tp_produto').val() != ""){
                          var id_tp_produto = $('#id_tp_produto').val();
                          text += "Tipo de Produto: "+$('#id_tp_produto option[value="'+id_tp_produto+'"]').text()+"/ ";
                      }
                      if($('#id_form_impressao').val() != ""){
                          var id_form_impressao = $('#id_form_impressao').val();
                          text += "Formato de Impressão: "+$('#id_form_impressao option[value="'+id_form_impressao+'"]').text()+"/ ";
                      }
                      if($('#id_tam_papel').val() != ""){
                          var id_tam_papel = $('#id_tam_papel').val();
                          text += "Tamanho de papel: "+$('#id_tam_papel option[value="'+id_tam_papel+'"]').text()+"/ ";
                      }
                      if($('#id_tam_chapa').val() != ""){
                          var id_tam_chapa = $('#id_tam_chapa').val();
                          text += "Tamanho de chapa: "+$('#id_tam_chapa option[value="'+id_tam_chapa+'"]').text()+"/ ";
                      }
                      if($('#id_montagem').val() != ""){
                          var id_montagem = $('#id_montagem').val();
                          text += "Montagem: "+$('#id_montagem option[value="'+id_montagem+'"]').text()+"/ ";
                      }
                      if($('#id_posicao').val() != ""){
                          var id_posicao = $('#id_posicao').val();
                          text += "Posição: "+$('#id_posicao option[value="'+id_posicao+'"]').text()+"/ ";
                      }
                      if($('#id_abertura').val() != ""){
                          var id_abertura = $('#id_abertura').val();
                          text += "Abertura: "+$('#id_abertura option[value="'+id_abertura+'"]').text()+"/ ";
                      }
                      if($('#pinca').val() != ""){
                          text += "Pinça: "+$('#pinca').val()+"/ ";
                      }
                      if($('#id_acabamento').val() != ""){
                          var id_acabamento = $('#id_acabamento').val();
                          text += "Acabamento: "+$('#id_acabamento option[value="'+id_acabamento+'"]').text()+"/ ";
                      }
                      if($('#id_costura_caderno').val() != ""){
                          var id_costura_caderno = $('#id_costura_caderno').val();
                          text += "Costura do caderno: "+$('#id_costura_caderno option[value="'+id_costura_caderno+'"]').text()+"/ ";
                      }
                      $('#pro_desc_produto').val(text);
                      $('#hidden_pro_desc_produto').val(text);
                      $('#dialog_descricao').dialog('close');
	                }
	              },
	              close: function(){
	              }
	        });
	    }else{
	        $('#pro_desc_produto').removeAttr('disabled');
	        $('#hidden_pro_desc_produto').remove();
	    }
	});

	//adiciona dados do select que esta no combo
	$('body').on('click','a.add', function(e){
	    e.preventDefault();
	    var title = $(this).attr('title_name');
	    var href = $(this).attr('href');
	    var $this = $(this);
        $('#dialog_nome').dialog({
            modal: true,
            title: title,
            position: [($(window).width() / 2) - (350 / 2), 250],
            width: "350",
            height: "170",
            buttons: {
                "Adicionar": function() {
                    var nome = $('#nome').val();
                    if(nome == ""){
                        alert("Preencha o nome.");
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: href,
                        data: { nome: nome},
                        success: function(data){
                            if (data.success){
                                for (x in (data.id))
                                {
                                    id= data.id[x];
                                }
                                $this.siblings('select').append('<option value="'+id+'">'+nome+'</option>').val(id);
                                $this.siblings('select').val(id);
                                $('#dialog_nome').dialog('close');
                            }else{
                                alert("Não foi possivel salvar.");
                            }
                        }
                    });
                }
              },
              close: function(){
                  $('#nome').val('');
              }
        });

	});
	// se o tipo de produto for chapa irá aparecer a div com o tamanho da chapa
	var id_tp_produto = $('#id_tp_produto').val();
	if(id_tp_produto == 1){
	    $('div.tam_chapa').show('slow');
	}else{
	    $('div.tam_chapa').hide('slow');
	}

	$('#id_tp_produto').change(function(){
	    var id_tp_produto = $('#id_tp_produto').val();
	    if(id_tp_produto == 1){
	        $('div.tam_chapa').show('slow');
	    }else{
	        $('div.tam_chapa').hide('slow');
	    }
	});

	//se o tipo de acabamento for costura irá aparecer o tipo de costura
	   var id_acabamento = $('#id_acabamento').val();
	    if(id_acabamento == 1){
	        $('div.acabamento').show('slow');
	    }else{
	        $('div.acabamento').hide('slow');
	    }

	    $('#id_acabamento').change(function(){
	        var id_acabamento = $('#id_acabamento').val();
	        if(id_acabamento == 1){
	            $('div.acabamento').show('slow');
	        }else{
	            $('div.acabamento').hide('slow');
	        }
	    });


	       //abre o dialog para cadastrar serviço no processo
        $("body").on("click", '#add_servico',function(event){
            $('div.dialog_servico').dialog({
                modal: true,
                open: function(event, ui) {
                    $('.decimal').setMask();
                    $('#quantidade_servico').setMask('9999999999');
                    $( "#servico" ).autocomplete({
                         source: "service/service/autocomplete",
                         minLength: 2,
                         select: function( event, ui ) {
                             $("#id_servico_selected").val(ui.item.id);
                         },
                         search: function( event, ui ) {
                             $("#id_servico_selected").val('');
                         }
                      });

                },
                title: "Cadastrar serviço",
                position: [($(window).width() / 2) - (500 / 2), 200],
                width: "500",
                height: "215",
                buttons: {
                    "Cancelar": function() {
                        $( this ).dialog( "close" );
                      },
                      "Adicionar": function() {

                          if( $('#id_servico_selected').val() == "" ){
                              alert('selecione um servico.');
                              return;
                          }
                              $.ajax({
                                  type: "POST",
                                  url: 'processo/processo-servico/form',
                                  data: {id_processo: $('#pro_id').val(), id_servico:$('#id_servico_selected').val(),
                                      quantidade: $('#quantidade_servico').val(), vl_unitario: $('#vl_unitario_servico').val(),
                                      total: $('#total_servico').val(), id: $('#id_processo_servico').val()},
                                  success: function(data){
                                      if( data.success){
                                          alert("Serviço cadastrado com sucesso.");
                                          $( 'div.dialog_servico' ).dialog( "close" );
                                          $('#table_servico').gridServico();
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
                  close: function(){
                      $("#id_servico_selected").val("");
                      $("#servico").val("");
                      $("#quantidade_servico").val("");
                      $("#vl_unitario_servico").val("");
                      $("#total_servico").val("");
                  }
            });

        });

        $('body').on('change', '#quantidade_servico, #vl_unitario_servico', function(){
            if($('#quantidade_servico').val() == ""){
                return;
            }
            var vl_unitario_servico = parseFloat($('#vl_unitario_servico').val().replace(/\./g, '').replace(',', '.'));
            var quantidade_servico  = parseFloat( $('#quantidade_servico').val() );
            $('#total_servico').val( decimal(vl_unitario_servico * quantidade_servico));
        });

        $('body').on('click', '.editar_servico', function(e){
            e.preventDefault();
            var id_processo_servico = $(this).attr('id_processo_servico');
            $.ajax({
                type: "POST",
                url: 'processo/processo-servico/get',
                data: {id: id_processo_servico},
                success: function(data){
                    $('#id_processo_servico').val(data.id_processo_servico);
                    $('#servico').val(data.nome_servico);
                    $('#id_servico_selected').val(data.id_servico);
                    $('#quantidade_servico').val(data.quantidade);
                    $('#vl_unitario_servico').val(data.vl_unitario ? data.vl_unitario.replace('.', ','):null);
                    $('#total_servico').val(data.total? data.total.replace('.', ','): null);
                    $('#add_servico').trigger('click');
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

        $('body').on('click', '.editar_servico', function(e){
            e.preventDefault();
            var id_processo_servico = $(this).attr('id_processo_servico');
            $.ajax({
                type: "POST",
                url: 'processo/processo-servico/get',
                data: {id: id_processo_servico},
                success: function(data){
                    $('#id_processo_servico').val(data.id_processo_servico);
                    $('#servico').val(data.nome_servico);
                    $('#id_servico_selected').val(data.id_servico);
                    $('#quantidade_servico').val(data.quantidade);
                    $('#vl_unitario_servico').val(data.vl_unitario ? data.vl_unitario.replace('.', ','):null);
                    $('#total_servico').val(data.total? data.total.replace('.', ','): null);
                    $('#add_servico').trigger('click');
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

        $('body').on('click', '.excluir_servico', function(e){
            e.preventDefault();
            var id_processo_servico = $(this).attr('id_processo_servico');
            if(confirm('Deseja realmente excluir este dado?')){
                $.ajax({
                    type: "POST",
                    url: 'processo/processo-servico/delete',
                    data: {id: id_processo_servico},
                    success: function(data){
                        if( data[0].type =="success"){
                            alert('Serviço excluído com sucesso.');
                            $('#table_servico').gridServico();
                        }else{
                            alert(data[0].text);
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
        });

        $('body').on('change', '#pro_quantidade, #pro_vlr_unt', function(){
            var pro_vlr_unt = parseFloat($('#pro_vlr_unt').val().replace(/\./g, '').replace(',', '.'));
            var pro_quantidade  = parseFloat( $('#pro_quantidade').val() );
            $('#pro_vlr_pedido').val( decimal(pro_vlr_unt * pro_quantidade));
        });

        $("body").on("click", '#saveAgrupadorFinanceiro',function(event){

            if($('#moe_id').val() == ""){

            	alert("Selecione uma moeda");return false;
            }

            if($('#tmv_id').val() == ""){

            	alert("Selecione o tipo de movimento finaneiro");return false;
            }

            if($('#pro_desc_produto').val() == ""){

            	alert("O processo necessita de uma descrição para efetuar o vínculo financeiro.");return false;
            }

        	$.ajax({
	            type: "POST",
	            url: "financial/agrupador-financeiro/form/",
	            data: $('#agrupadorFinanceiroForm').serialize()+"&pro_id="+$("#pro_id").val()+"&fin_descricao="+$("#pro_desc_produto").val()+"&id_empresa="+$("#empresas_id").val(),
	            beforeSend: function(){
	                $("#load").show();
	            },
	            success: function(data){

	            	alert("Rgistro Salvo Com Sucesso");
	                $('#table_financial').gridItem();
		            $("#agrupadorFinanceiroBox").dialog( "close" );

	            },
	            complete: function(){
	                $("#load").hide();
	            },
	            error: function(){
	                alert("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err4");
	            }

	        });
        });

        $('#tmv_id').change(function(){

        	movimentoFinanceiro = $("#tmv_id").val();

    		if (movimentoFinanceiro != ""){

    			$.ajax({
    	            type: "POST",
    	            url: 'financial/plano-contas/get-pairs-per-type',
    	            data: { type: movimentoFinanceiro},
    	            success: function(data){

    	            	if (data.success == "true"){

    	            		$("#plc_id").empty();

    	            		$.each( data.data, function( key, value ) {

    	            			$("#plc_id").prepend('<option value="'+key+'">'+value+'</option>');

    	            		});

    	            		$("#plc_id").prepend('<option selected = "selected" value="">Selecione</option>');

    	            	}else{

    	            		alert("Contacte o administrador. Os planos de contas não puderam localizados");
    	            	}
    	            }
    	        });
    		}

    	});
});


$.fn.gridServico = function(){
    var id_processo= $('#pro_id').val();
    var isCompany = $('#isCompany').val();
    var $this = $(this);
    /* se quem estiver logado for uma empresa não irá mostrar a grid */
    if(isCompany == 1){
        return;
    }
    $.ajax({
        type: "GET",
        url: "processo/processo-servico/grid/pro_id/"+id_processo,
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