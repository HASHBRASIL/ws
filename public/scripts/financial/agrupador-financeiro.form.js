$(document).ready(function(){
	FormWizard.init();

	$('.decimal').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});
	calculateValor();

	if($("#finIdForDialog").val() != ""){

		$.ajax({
            type: "POST",
            url: 'financial/financial/get',
            data: {fin_id: $("#finIdForDialog").val()},
            success: function(data){

            	if (data.success == true){

            	  $("#fin_fin_id").val(data['fin_id']);
              	  $("#fin_fin_valor").val(data['fin_valor']);
              	  $("#fin_fin_num_doc_os").val(data['fin_num_doc_os']);
              	  $("#fin_tid_id").val(data['tid_id']);
              	  $("#fin_tie_id").val(data['tie_id']);
              	  $("#fin_fin_numero_doc").val(data['fin_numero_doc']);
              	  $("#fin_fin_observacao").val(data['fin_observacao']);
              	  $("#fin_fin_descricao").val(data['fin_descricao']);
              	  $("#fin_con_id").val(data['con_id']);
              	  $("#fin_stf_id").val(data['stf_id']);
              	  $("#fin_fin_competencia").val(data['fin_competencia']);
              	  $("#fin_fin_emissao").val(data['fin_emissao']);
              	  $("#fin_fin_vencimento").val(data['fin_vencimento']);
              	  $("#fin_fin_compensacao").val(data['fin_compensacao']);
              	  $("#fin_plc_id").val(data['plc_id']);
              	  $("#fin_cec_id").val(data['cec_id']);
              	  $("#fin_ope_id").val(data['ope_id']);
              	  $("#fin_grupo_id").val(data['grupo_id']);
              	  $("#empresa_sacado_credor").val(data['nome_razao']);
            	  $("#empresa_sacado_selected").val(data['id_empresa']);

              	  showBaseHidden();

            	}else{
            		alert("Contacte o administrador. Não foi possível fazer um pagamento. AjxErr3");return false;
            	}
            }
        });

	}

	$("#fin_con_id").change(function() { /*parei fazendo o a data de compensacao aparecer somente quando conta estiver diferente de null **/

		if($("#fin_con_id").val() != ""){

			$("#fin_compensacaoDiv").show();

		}else{
			$("#fin_compensacaoDiv").hide();
			$("#fin_fin_compensacao").val("");

		}

	});


	var movimentoFinanceiro = $("#tmv_id").val();
	var transferencia = $("#transferencia").val();
	var planoConta = $("#plano_conta_selected").attr("value");

	if (movimentoFinanceiro != ""){

		if(movimentoFinanceiro == /*a receber*/"2" && transferencia == '0' ){
        	$("#tmvType").empty().text("A receber");
        	$("#labelCliente").empty().text("Entidade");

        }else if (movimentoFinanceiro == /*a pagar*/"1" && transferencia == '0' ){
        	$("#tmvType").empty().text("A pagar");
        	$("#labelCliente").empty().text("Entidade");

        }else{
        	$("#tmvType").empty().text("Transferência");
        	$("#labelCliente").empty().text("Transferência");
        	$("#restaPagarCaixa").hide();
        	$("#dashboardPrice").hide();
        	$("#caixa").hide();
        }

		var restaPagar = $("#restaPagar").attr("value");
		$("#dashboardPrice").empty().text($("#moe_id option:selected" ).text()+" "+restaPagar);

		$.ajax({
	        type: "POST",
	        url: 'financial/plano-contas/get-pairs-per-type',
	        data: { type: movimentoFinanceiro},
	        success: function(data){

	        	if (data.success == "true"){
	        		$.each( data.data, function( key, value ) {

	        			$("#plc_id").prepend('<option value="'+key+'">'+value+'</option>');
	        			$("#fin_plc_id").prepend('<option value="'+key+'">'+value+'</option>');

	        		});
	        		if (planoConta != "" && $.isNumeric(planoConta)){
	        			$('#plc_id option[value='+planoConta+']').remove();
	        			$("#plc_id").prepend('<option selected = "selected" value="'+planoConta+'">'+$("#plano_conta_selected").attr("name")+'</option>');
	        		}
	        	}else{

	        		alert("Contacte o administrador. O plano de conta não pode ser carregado");
	        	}
	        }
	    });
	}else{

		$("#dashboard_tmv").addClass("red");
    	$("#dashboard_tmv").removeClass("green bg-grey");
    	$("#tmvType").empty().text("A pagar");
    	$("#labelCliente").empty().text("Entidade");
    	$("#tmv_id").val("1");/*a pagar*/

	    //o que faz o dialog do tmv abrir
	    $('#tipo_movimento').dialog({
	        modal: true,
	        dialogClass: 'ui-dialog-blue',
	        position: [($(window).width() / 2) - (450 / 2), 200],
	        resizable: true,
	        title: "Transação",
	        width: 300,
	        height: 200,
	        buttons: [{
			        	'class' : 'btn',
			        	"text" : "OK",
			        		click: function() {

			        			if ($("#tmv_id").val() == ""){
			        				alert("Selecione o tipo de movimento da transação");
			        			}else{
			        				$( this ).dialog( "close" );
			        			}
			        		}
	                  }
	        ],
	        close: function(){
                loadPlanoContas();
	         }
	    });

	}

	//aba Historico

	if ($('#id_agrupador_financeiro').val()){
		//Historico do Agrupador
        $.ajax({
            type: "GET",
            url: "financial/agrupador-financeiro/historico/id_agrupador_financeiro/"+$('#id_agrupador_financeiro').val(),
            success: function(data){
                    $("#grid-historico").html(data);
            }
        });
	}

	//Modal Historico Tickets

	$('body').on("click",'.historicoTicket', function(e){
		e.preventDefault();
		var id = $(this).attr("id");
		$("#grid-historicoTicketModal").html('');

		$('#historicoTicketModal').dialog({
			modal: true,
	        dialogClass: 'ui-dialog-blue',
	        resizable: false,
	        title: "Historico do Ticket "+id,
	        width: '1000',
            position: [($(window).width() / 2) - (1000 / 2), 200],
	        height: 'auto',
	        position: "center",
	        open: function(){
	        	$.ajax({
	                type: "GET",
	                url: "financial/financial/historico/fin_id/"+id,
	                success: function(data){
	                        $("#grid-historicoTicketModal").html(data);
	                }
	            });
	        	$( window ).resize(function() {
	        	    $("#historicoTicketModal").each(function () {
	        	        $( this ).dialog("option","position",$(this).dialog("option","position"));
	        	    });
	        	});
	        },
	        buttons: [{
			        	'class' : 'btn',
			        	"text" : "OK",
			        		click: function() {
		        				$( this ).dialog( "close" );
			        		}
	                  }
	        ],
	        close: function(){

	         }
	    });
	});

    //toggleButtons tmv
    $('.tmv-toggle-button').toggleButtons({
        width: 170,
        label: {
            enabled: "A receber",
            disabled: "A pagar",

        },
        style: {
            // Accepted values ["primary", "danger", "info", "success", "warning"] or nothing
            enabled: "success",
            disabled: "danger"
        },
        onChange: function ($el, status, e) {
            if(status){
            	$("#tmv_id").val(/*a receber*/"2");
            	$("#dashboard_tmv").addClass("green");
            	$("#dashboard_tmv").removeClass("red bg-grey");
            	$("#tmvType").empty().text("A receber");
            	$("#labelCliente").empty().text("Entidade");

            }else{
            	$("#tmv_id").val(/*a pagar*/"1");
            	$("#dashboard_tmv").addClass("red");
            	$("#dashboard_tmv").removeClass("green bg-grey");
            	$("#tmvType").empty().text("A pagar");
            	$("#labelCliente").empty().text("Entidade");

            }
            loadPlanoContas();
        }
    });

    $( "#nome_pessoa_cliente" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#id_pessoa_cliente").val(ui.item.id);
        },
        search: function( event, ui ) {
        }
      });

	$('#abrirPagamento').click(function(e){

		e.preventDefault();
		showBaseHidden();

		if($("#nome_empresa_cliente").val() != "" && $("#empresa_sacado_credor").val() == "" ){

      		$("#empresa_sacado_credor").val($("#nome_empresa").val());
		}

	});

	$('#emitirRecibo').click(function(e){

		e.preventDefault();
		window.open("financial/financial/recibo/fin_id/"+$("#fin_fin_id").val());

	});

	$("body").on('click','.editFinancial',function(e){

		e.preventDefault();
		$.ajax({
            type: "POST",
            url: 'financial/financial/get',
            data: {fin_id: $(this).attr("name")},
            success: function(data){

            	if (data.success == true){

            	  $("#fin_fin_id").val(data['fin_id']);
              	  $("#fin_fin_valor").val(data['fin_valor']);
              	  $("#fin_fin_num_doc_os").val(data['fin_num_doc_os']);
              	  $("#fin_tid_id").val(data['tid_id']);
              	  $("#fin_tie_id").val(data['tie_id']);
              	  $("#fin_fin_numero_doc").val(data['fin_numero_doc']);
              	  $("#fin_fin_observacao").val(data['fin_observacao']);
              	  $("#fin_fin_descricao").val(data['fin_descricao']);
              	  $("#fin_con_id").val(data['con_id']);
              	  $("#fin_stf_id").val(data['stf_id']);
              	  $("#fin_fin_competencia").val(data['fin_competencia']);
              	  $("#fin_fin_emissao").val(data['fin_emissao']);
              	  $("#fin_fin_vencimento").val(data['fin_vencimento']);
              	  $("#fin_fin_compensacao").val(data['fin_compensacao']);
              	  $("#fin_plc_id").val(data['plc_id']);
              	  $("#fin_cec_id").val(data['cec_id']);
              	  $("#fin_ope_id").val(data['ope_id']);
              	  $("#fin_grupo_id").val(data['grupo_id']);
              	  $("#empresa_sacado_credor").val(data['nome_razao']);
              	  $("#empresa_sacado_selected").val(data['id_empresa']);

              	  showBaseHidden();

            	}else{
            		alert("Contacte o administrador. Não foi possível fazer um pagamento. AjxErr3");return false;
            	}
            }
        });

	});

	$('.datepickerMonth').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        format: "dd/mm/yyyy",
        minViewMode: 1,
        autoclose: true,
        viewMode: 1,
        language: "pt-BR",
        startDate: '-3y',
        onClose: function(dateText, inst) {
        	if ($(this).val() != ""){
        		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
        	}
        }
    });
	$('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
	});
	$('body').on('click', '.deleteMoldal', function(e){
		e.preventDefault();
		var id = $(this).attr("id");
		$("#valueDelete").prepend(id);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-grey',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir Pagamento",
            width: 450,
            height: 200,
            buttons: [
                      {
		                 'class' : 'btn gree',
		                 "text" : "Cancelar",
		                click: function() {
		                  $( this ).dialog( "close" );
		                }
                      },
                      {
		                 'class' : 'btn red',
		                 "text" : "Excluir",
                    	  click: function() {
                    		  e.preventDefault();
                    			$.ajax({
                    	            type: "POST",
                    	            url: 'financial/financial/delete',
                    	            data: { fin_id: id},
                    	            success: function(data){

                    	            	if (data[0].type == "success"){

                    	            		alert("Registro Excluído Com Sucesso");
                    	            		$("#fin_id_"+id).fadeOut("slow").remove();
                    	            		window.location.href="financial/agrupador-financeiro/form/id_agrupador_financeiro/"+$("#id_agrupador_financeiro").val();


                    	            	}else{
                    	            		alert("Contacte o administrador. O Registro não pode ser excluído AjaxErr2");
                    	            	}
                    	            },
                    	            complete: function(){

                    	            	$( '#dialog_delete' ).dialog( "close" );
                    	            }
                    	        });
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
	});

	$('body').on('click', '.duplicarFinancial', function(e){
		e.preventDefault();
		var id = $(this).attr("name");
		$('#fin_id').val(id);
        $('#dialogDuplicar').dialog({
            modal: true,
            dialogClass: 'ui-dialog-blue',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Duplicar Tk",
            width: 450,
            height: 210,
            buttons: [
                      {
		                 'class' : 'btn gree',
		                 "text" : "Cancelar",
		                click: function() {
		                  $( this ).dialog( "close" );
		                }
                      },
                      {
		                 'class' : 'btn red',
		                 "text" : "Duplicar",
                    	  click: function() {
                    		  e.preventDefault();
                    			$.ajax({
                    	            type: "POST",
                    	            url: 'financial/financial/duplicar-tks-ajax',
                    	            data: $('.form-tk').serialize(),
                    	            success: function(data){
                    	            	if (data.success == true){
                    	            		location.reload();
                    	            	}else{
                    	            		$.messageBox(data.msg, 'error');
                    	            	}
                    	            },
                    	            complete: function(){
                    	            	$('vencimento').val('');
                    	            	$('competencia').val('');
                    	            	$( '#dialogDuplicar' ).dialog( "close" );
                    	            }
                    	        });
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
	});


	if($('#id_agrupador_financeiro').val() != ""){
	    $.ajax({
            type: "GET",
            url: 'financial/financial/grid-ajax/id_agrupador_financeiro/'+$('#id_agrupador_financeiro').val(),
            success: function(data){
                $('#table-financial').html(data);
            }
        });
	}
});

function calculateValor(){

	var valorTotal = $("#valorTotal").attr("value");
	var fin_valor = $("#fin_valor").val();

	if ($("#id_agrupador_financeiro").val() != "" ){

		if(valorTotal == fin_valor){

			$("#fin_valor").css("background", "rgb(202, 236, 202)");

		}else{

			if($("#tmv_id").val() == 3 /*transferencia*/){

				$("#fin_valor").css("background", "#ffb848");

			}else{

				$("#fin_valor").css("background", "rgb(228, 164, 164)");
			}
		}
	}

}

function loadPlanoContas(){
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
}

function showBaseHidden(){


	$('#baseHidden').dialog({
        modal: true,
        open: function(event, ui) {

        	if($("#fin_con_id").val() != "" && $("#fin_fin_compensacao").val() != "" ){

      		  $("#boxRecibo").show();
        	}

        	if($("#fin_con_id").val() != ""){

      			$("#fin_compensacaoDiv").show();

      		}

        	if ($("#fin_fin_id").val()  == ""){

        		$("#boxRecibo").hide();

        	}

        	if ($("#fin_con_id").val()  != "" && $("#fin_fin_compensacao").val() != ""){

        		//$("#fin_con_id").attr("disabled", "disabled");
        		//$("#fin_fin_compensacao").attr("disabled", "disabled");
        	}

        	$( "#empresa_sacado_credor" ).autocomplete({
                source: "empresa/empresa/autocomplete",
                minLength: 2,
                select: function( event, ui ) {
                    $("#empresa_sacado_selected").val(ui.item.id);
                },
                search: function( event, ui ) {
                }
            });

        	$('.decimal').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});

        	//$("#fin_fin_vencimento, #fin_fin_compensacao, #fin_fin_emissao, #fin_fin_competencia").setMask({mask:'99/99/9999',autoTab: false});

        	$("input").blur();

        	$( ".datepicker" ).datepicker({
      		  defaultDate: "+1w",
      	      changeMonth: true,
              format: "dd/mm/yyyy",
              language: "pt-BR",
        	});

        },
        dialogClass: 'ui-dialog-blue',
        position: [($(window).width() / 2) - (450 / 2), 200],
        resizable: true,
        title: "Ticket",
        width: 750,
        height: 650,
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
                     "text" : "Salvar",
                      click: function() {

                    	  /*if ($("#fin_stf_id").val() == ""){
                    		  alert("O status financeiro é obrigatório");
                    		  return false;
                    	  }*/
                    	  if ($("#fin_fin_valor").val() == ""){
                    		  alert("O valor é obrigatório");
                    		  return false;
                    	  }
                    	  if ($("#fin_fin_descricao").val() == ""){
                    		  alert("A descrição é obrigatória");
                    		  return false;
                    	  }

                    	  if ($("#fin_cec_id").val() == ""){

                    		  $("#fin_cec_id").val($("#cec_id").val());
                    	  }
                    	  if ($("#fin_plc_id").val() == ""){

                    		  $("#fin_plc_id").val($("#plc_id").val());
                    	  }
                    	  if ($("#fin_ope_id").val() == ""){

                    		  $("#fin_ope_id").val($("#ope_id").val());
                    	  }
                    	  if ($("#fin_grupo_id").val() == ""){

                    		  $("#fin_grupo_id").val($("#grupo_id").val());
                    	  }
                    	  if ($("#empresa_sacado_selected").val() == ""){

                    		  $("#empresa_sacado_selected").val($("#id_empresa").val());
                    	  }

                  		$.ajax({
                              type: "POST",
                              url: 'financial/financial/form',
                              data: { fin_id: $("#fin_fin_id").val(), id_agrupador_financeiro: $("#id_agrupador_financeiro").val(),fin_valor:$("#fin_fin_valor").val(),fin_num_doc_os: $("#fin_fin_num_doc_os").val(),
                            	  				tie_id: $("#fin_tie_id").val(),fin_numero_doc: $("#fin_fin_numero_doc").val(),tid_id: $("#fin_tid_id").val(), fin_observacao: $("#fin_fin_observacao").val()  ,
                            	  				fin_descricao: $("#fin_fin_descricao").val()  , fin_competencia: $("#fin_fin_competencia").val()  ,con_id: $("#fin_con_id").val(),fin_emissao: $("#fin_fin_emissao").val(),
                            	  				/*stf_id: $("#fin_stf_id").val("1"),*/fin_vencimento: $("#fin_fin_vencimento").val(), fin_compensacao: $("#fin_fin_compensacao").val(), plc_id : $("#fin_plc_id").val(),
                            	  				cec_id: $("#fin_cec_id").val(), ope_id: $("#fin_ope_id").val(), grupo_id: $("#fin_grupo_id").val(), empresa_sacado_selected : $("#empresa_sacado_selected").val() },
                              success: function(data){

                              	if (data.success == true){
                              		alert("Registro Salvo Com Sucesso");
                          	        $.ajax({
                          	            type: "GET",
                          	            url: 'financial/financial/grid-ajax/id_agrupador_financeiro/'+$('#id_agrupador_financeiro').val(),
                          	            success: function(data){
                          	                $('#table-financial').html(data);
                          	              $('#baseHidden').dialog('close');
                          	            }
                          	        });
                              	}else{
                              		alert("Contacte o administrador. Não foi possível fazer um pagamento. AjxErr2");
                              	}
                              }
                          });
                    }
            }
          ],
          close: function(){

        	  $("#fin_fin_id").val("");
        	  $("#fin_fin_valor").val("");
        	  $("#fin_fin_num_doc_os").val("");
        	  $("#fin_tie_id").val("");
        	  $("#fin_fin_numero_doc").val("");
        	  $("#fin_tid_id").val("");
        	  $("#fin_fin_observacao").val("");
        	  $("#fin_fin_descricao").val();
        	  $("#fin_fin_competencia").val();
        	  $("#fin_con_id").val("");
        	  $("#fin_fin_emissao").val("");
        	  /*$("#fin_stf_id").val("");*/
        	  $("#fin_fin_vencimento").val("");
        	  $("#fin_fin_compensacao").val("");
        	  $("#fin_fin_descricao").val("");
        	  $("#fin_fin_competencia").val("");
        	  $("#fin_cec_id").val("");
        	  $("#fin_plc_id").val("");
        	  $("#fin_grupo_id").val("");
        	  $("#fin_ope_id").val("");
        	  $("#empresa_sacado_credor").val("");
          	  $("#empresa_sacado_selected").val("");
          	  $("#fin_con_id").removeAttr("disabled");
          	  $("#fin_fin_compensacao").removeAttr("disabled");
    		  $("#fin_compensacaoDiv").hide();
    		  $("#boxRecibo").hide();

          }
    });


}

var FormWizard = function () {

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            var form = $('#form_agrupador');
            var error = $('.alert-error', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                	fin_valor: {
                        required: true
                    },
                    fin_descricao:{
                    	required: true
                    }
                },
                messages: { // custom messages for radio buttons and checkboxes
                	fin_valor: {
                        required: "Campo obrigatório."
                    },
                    fin_descricao: {
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
                    App.scrollTo($('.page-title'));
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

                    App.scrollTo($('.page-title'));
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

                    App.scrollTo($('.page-title'));
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
        }

    };

}();