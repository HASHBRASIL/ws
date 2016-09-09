$(document).ready(function(){

	$( "#buttonShake" ).mouseenter(function() {
		$( "#buttonShake" ).effect( "shake", {distance:2} );
	});

	$( "#buttonShake").click(function(e){

		$( "#buttonShake" ).effect( "shake", {distance:2} );

		var tempContaDe = $("#de_con_id").val();
		var tempEmpresaSacadoDe = $("#de_empresa_sacado_selected").val();
		var tempEmpresaSacadoTextDe = $("#de_empresa_sacado").val();

		$("#de_con_id").val($("#para_con_id").val());
		$("#de_empresa_sacado_selected").val($("#para_empresa_sacado_selected").val());
		$("#de_empresa_sacado").val($("#para_empresa_sacado").val());
		$("#para_empresa_sacado").val(tempEmpresaSacadoTextDe);
		$("#para_con_id").val(tempContaDe);
		$("#para_empresa_sacado_selected").val(tempEmpresaSacadoDe);

	});

	$('.autoCompleteEmpresa').click(function(e){

		var id = $(this).attr("id");

		$( ".autoCompleteEmpresa" ).autocomplete({
	        source: "empresa/empresa/autocomplete",
	        minLength: 2,
	        select: function( event, ui ) {

	        	if (id == "de_empresa_sacado" ){
	        		$("#de_empresa_sacado_selected").val(ui.item.id);
	        	}else{
	        		$("#para_empresa_sacado_selected").val(ui.item.id);
	        	}

	        },
	        search: function( event, ui ) {

	        	if (id == "de_empresa_sacado" ){
	        		$("#de_empresa_sacado_selected").val("");
	        	}else{
	        		$("#para_empresa_sacado_selected").val("");
	        	}

	        }
	      });

	});

	$( "#fin_valor").change(function(){

		$( "#de_valor").val($( "#fin_valor").val());
		$( "#ate_valor").val($( "#fin_valor").val());

	});

	//$('.decimal').setMask();
    $('.decimal').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});



	$('.transContas').click(function(e){

		$('#baseHidden').dialog({
	        modal: true,
	        dialogClass: 'ui-dialog-blue',
	        resizable: true,
	        title: "Detalhamento de Transação",
	        width: 950,
	        height: 430,
	        open: function(event, ui) {

	        	$(".datepicker").setMask({mask:'99/99/9999',autoTab: false});

	        	$( ".datepicker" ).datepicker({
	      		  defaultDate: "+1w",
	      	      changeMonth: true,
	              format: "dd/mm/yyyy",
	              language: "pt-BR",
	        	});

	        },
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
	                     "text" : "Fazer Transferência",
	                      click: function() {

	                    	  if($( "#fin_valor").val() == ""){

	                    		  alert("Defina um valor para a transferência");return false;

	                    	  }
	                    	  if($( "#dateTransacao").val() == ""){

	                    		  alert("Selecione uma data para a transferência");return false;
	                    	  }
	                    	  if($( "#de_con_id").val() == ""){

	                    		  alert("Selecione a conta origem da transferência");return false;
	                    	  }
	                    	  if($( "#para_con_id").val() == ""){

	                    		  alert("Selecione a conta destino da transferência");return false;
	                    	  }
	                    	  if($( "#de_empresa_sacado_selected").val() == ""){

	                    		  alert("Inclua o pagador");return false;
	                    	  }
	                    	  if($( "#para_empresa_sacado_selected").val() == ""){

	                    		  alert("Inclua o favorecido");return false;
	                    	  }

	                    	  $("#transferenciaConta").submit();
	                    }
	            }
	          ],
	          close: function(){

	        	  $("#fin_valor").val("");
	        	  $("#de_con_id").val("");
	        	  $("#para_con_id").val("");
	        	  $("#de_empresa_sacado").val("");
	        	  $("#para_empresa_sacado").val("");
	        	  $("#de_empresa_sacado_selected").val("");
	        	  $("#para_empresa_sacado_selected").val("");

	          }
	    });

	});

	$('body').on('click', '.duplicar', function(e){
		e.preventDefault();
		var id = $(this).attr("id");
		$("#valueDelete").prepend(id);
        $('#dialog_duplicar').dialog({
            modal: true,
            dialogClass: 'ui-dialog-blue',
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
		                 "text" : "Duplicar",
                    	  click: function() {
                    		  e.preventDefault();
                    			$.ajax({
                    	            type: "POST",
                    	            url: 'financial/agrupador-financeiro/duplicar-transacao',
                    	            data: { id_agrupador_financeiro: id},
                    	            success: function(data){
                    	            	if (data.success == true){
                    	            		location.reload();
                    	            	}else{
                    	            		$.messageBox(data.msg, 'error');
                    	            	}
                    	            },
                    	            complete: function(){

                    	            	$( '#dialog_duplicar' ).dialog( "close" );
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

	$('#sample_1').on('click', ' tbody td .row-details', function () {
        var id_agrupadorFinanceiro = $(this).attr('data-id_agrupador_financeiro');
        $(".details").remove();
        if($(this).hasClass("row-details-open")){
            $(this).addClass("row-details-close").removeClass("row-details-open");
        }else{
            $('.row-details').removeClass("row-details-open");
            var $this = $(this);
            $.get('financial/financial/grid-editable/id_agrupador_financeiro/'+id_agrupadorFinanceiro, function(data){

                var sOut = '<tr  class="details" ><td colspan="12" >';
                sOut += data;
                sOut += '</td></tr>';
                $(sOut).insertAfter($this.parents('tr'));
                $('.details').slideDown("slow");
                $this.addClass("row-details-open");
            });
        }
    });

});