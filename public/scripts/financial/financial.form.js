$(document).ready(function(){

	$("#quickSearchButton").click(function() {
		  var id = $("#quickSearch").val();
			$.ajax({
		        type: "POST",
		        url: "financial/financial/quick-search-ajax/id/"+id,
		        data: { id: id},
		        success: function(data){

		        	if (data.success == "true"){

		        		 window.location.href="financial/financial/form/fin_id/"+id+"/id_agrupador_financeiro/"+data.financial.id_agrupador_financeiro;
		        	}else{
		        		alert("Código Não encontrado.");
		        	}
		        }
		    });
	});

	/* Regra de negocio que permite apenas que seja emitido recibo quando status financeiro for liquidado existindo data de compensacao e id de financeiro*/
	if ($("#stf_id").val() == 1/*liquidado*/ && $("#fin_compensacao").val() != "" && $("#fin_id").val() != "" ){
		$("#emitirRecibo").show();
	}

	var id = $("#fin_id").val();

	if ($.isNumeric(id)){

		$.ajax({
	        type: "POST",
	        url: 'financial/financial/next-or-previous-id',
	        data: { id: id},
	        success: function(data){

	        	if (data.success == "true"){

	        		$.each( data.data, function( key, value ) {

	        			if ($.isNumeric(value)){

	        				$("#"+key).attr("href", "financial/financial/form/id/"+value);
	        				$("#"+key).fadeIn("slow");
	        			}

	        		});
	        	}
	        }
	    });

	}

	var movimentoFinanceiro = $("#tmv_id").val();
	var planoConta = $("#plano_conta_selected").attr("value");

	if (movimentoFinanceiro != ""){

		$.ajax({
	        type: "POST",
	        url: 'financial/plano-contas/get-pairs-per-type',
	        data: { type: movimentoFinanceiro},
	        success: function(data){

	        	if (data.success == "true"){
	        		$.each( data.data, function( key, value ) {

	        			$("#plc_id").prepend('<option value="'+key+'">'+value+'</option>');

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
	}

	$("#tmv_id").change(function(){

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


	            		if (movimentoFinanceiro == 1/*a pagar*/){

	            			$("#fin_valor").addClass("error");
	            			$("#fin_valor").removeClass("success");
	            		}else{//a receber

	            			$("#fin_valor").removeClass("error");
	            			$("#fin_valor").addClass("success");
	            		}

	            	}else{

	            		alert("Contacte o administrador. Os planos de contas não puderam localizados");
	            	}
	            }
	        });
		}
	});

	$('.decimal').setMask();

    $( "#empresa_sacado" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#empresa_sacado_selected").val(ui.item.id);
        },
        search: function( event, ui ) {
        }
      });

	$("#fin_emissao, #data_emissao2, #fin_vencimento, #fin_compensacao, #fin_competencia").setMask({mask:'99/99/9999',autoTab: false});


	$('.datepickerMonth').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        onClose: function(dateText, inst) {
        	if ($(this).val() != ""){
        		var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
        	}
        }
    });


	$( ".datepicker" ).datepicker({
		  defaultDate: "+1w",
	      changeMonth: true,
	});

});