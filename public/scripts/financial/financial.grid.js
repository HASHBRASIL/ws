$(document).ready(function(){

	var arrayIds = [];
	var saveBoolean = false;

	$("input[type=checkbox]").on("click", function(event){

		if (this.checked){

			arrayIds.push( $(this).val());
			console.log(arrayIds);

		}else{
			var arrayTemp = [];

			for ( var int = 0; int < arrayIds.length; int++) {

				if ($(this).val() != arrayIds[int] ){
					arrayTemp.push( arrayIds[int]);
				}
			}
			arrayIds = null;
			arrayIds = arrayTemp;
			console.log(arrayIds);
		}


	});

	$('#abrirMultiplaEdicao').click(function(e){

		e.preventDefault();
        $('#baseHidden').dialog({
            modal: true,
            open: function(event, ui) {

	        	$("#fin_vencimento, #fin_compensacao").setMask({mask:'99/99/9999',autoTab: false});

	        	for ( var int = 0; int < arrayIds.length; int++) {

	        		$("#idsBox").prepend("<i>"+arrayIds[int]+" / "+"</i> ");

				}

	        	$("input").blur();

	        	$( ".datepicker" ).datepicker({
	      		  defaultDate: "+1w",
	      	      changeMonth: true,
	        	});

	        },
            dialogClass: 'ui-dialog-blue',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Editar Múltiplo",
            width: 550,
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
                        	  if ($("#stf_id").val() == ""){

                      			alert("O status financeiro é obrigatório");
                      			return false;
                      		}

                      		if(arrayIds.length <= 0){

                      			alert("Nenhum financeiro foi selecionado");
                      			return false;

                      		}

                      		if ($("#stf_id").val() == ""){

                      			alert("O status financeiro é obrigatório");
                      			return false;
                      		}

                      		$.ajax({
                                  type: "POST",
                                  url: 'financial/financial/quick-update-ajax',
                                  data: { id: arrayIds,con_id: $("#con_id").val(),fin_emissao: $("#fin_emissao").val(),fin_nota_fiscal: $("#fin_nota_fiscal").val(), pago: $("#pago").val(), grupo_id: $("#grupo_id").val(), plc_id: $("#plc_id").val(), cec_id: $("#cec_id").val(), ope_id: $("#ope_id").val(), fin_vencimento: $("#fin_vencimento").val(), fin_compensacao: $("#fin_compensacao").val()},
                                  success: function(data){

                                  	if (data.success == true){
                                  		$("#successBox").fadeIn('slow').fadeOut(4000);
                                  	}else{
                                  		alert("Contacte o administrador. Não foi possível fazer a edição rápida");
                                  	}
                                  }
                              });

                      		saveBoolean = true;
                        }
                }
              ],
              close: function(){

            	  $("#idsBox").empty();
	        	  if (saveBoolean == true){

		             window.location.href=document.URL;
		          }
              }
        });

	});

	var searchField = $("#searchField option:selected").val();

	insertAndRemoveDatePiccker();

	function insertAndRemoveDatePiccker(){

		if (searchField == 'fin_emissao' || searchField == 'fin_vencimento' || searchField == 'fin_compensacao'){

			$( "#searchString" ).datepicker({
				  defaultDate: "+1w",
			      changeMonth: true,
			});

		}else{

			$( "#searchString"  ).datepicker( "destroy" );
			$( "#searchString"  ).removeClass("hasDatepicker");

		}

	}

	$("#searchField").change(function() {
		$( "#searchString"  ).val('');
		searchField = $("#searchField option:selected").val();
		insertAndRemoveDatePiccker();

	});

});