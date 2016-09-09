$(document).ready(function(even){
	
	FormWizard.init();
	
	$('.decimal').setMask(); 
	$('.date').setMask();
	$( "#nome_empresa" ).autocomplete({
		source: "/empresa/empresa/autocomplete",
		minLength: 2,
		select: function( event, ui ) {
		    $("#id_empresa").val(ui.item.id);
		},
		search: function( event, ui ) {
		}
	});

	$('.dialog_tk').dialog({
        modal: true,
        title: 'Ticket de Transação',
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        width: 650,
        height: 340,
        autoOpen: false,
        open: function(){
            var tipoModelo = $("#tipoModelo").val(); 
        	$( "#fin_descricao" ).autocomplete({
        		source: "/rh/folha-de-pagamento/autocomplete-modelo/tipo/" + tipoModelo,
        		minLength: 2,
        		select: function( event, ui ) {
        			$(".referencia-hide").show();
        			$(".referencia").empty();
        		    $("#id_rh_modelo_sintetico").val(ui.item.id);
        		    $(".referencia").append(ui.item.referencia);
        		},
        		search: function( event, ui ) {
        		}
        	});
        },
        buttons: [
                  {
                     'class' : 'btn red',
                     "text" : "Cancelar",
                     click: function() {
                    	 $(".referencia-hide").hide();
                    	 $(".referencia").empty();
                    	 $(this).dialog('close');              
                     }
                  },
                  {
                     'class' : 'btn green',
                     "text" : "Salvar",
                      click: function() {
                    	  var vl = $('#fin_valor').val();
                    	  var des = $('#fin_descricao').val();
                    	  var emicao = $('#fin_emissao').val();
                    	  var validade = $('#fin_vencimento').val();
                    	  var d1 = new Date(emicao);
                    	  var d2 = new Date(validade);
                    	  if(vl == '0,00'){
                    		  alert('O valor e obrigatorio');
                    		  return false;
                    	  }
                    	  else if(des == ''){
                    		  alert('O Modelo Sintetico e obrigatorio');
                    		  return false;
                    	  }
                    	  else if(d1 > d2){
                    		  alert('Data de vencimento tem que ser maior que a de emissão.');
                    	  }
                    	  else {
                    		  $.ajax({
                    			  type : "POST",
                    			  url : "/rh/modelo-sintetico/validar-campos-ajax",
                    			  data : $('.form-tk').serialize(),
                    			  success : function(data){
                    				  if(data.success == true){
                    					  if(data.resposta == true){
                    						  $.ajax({
                    							  type : "POST",
                    							  url : "/financial/financial/form",
                    							  data : $('.form-tk').serialize(),
                    							  success : function(data){
                    								  alert("Ticket inserido com sucesso.");
                    								  window.location.href="/rh/folha-de-pagamento/form/id_rh_folha_de_pagamento/"+$('#id_rh_folha_de_pagamento').val();
                    							  }
                    						  });
                    						  return true;
                    					  } else if(data.resposta == false) {
                    						  alert(data.message);
                    						  return false;
                    					  }
                    				  } else if (data.success == false){
                    					  alert('Contate o administrador.');
                    				  }
                    			  }
                    		  });
                    	  }
                    }
            }
          ],
          close: function(){
        	$("#fin_id").val('');
  			$("#fin_valor").val('');
  			$("#id_agrupador_financeiro").val('');
  			$("#fin_competencia").val('');
  			$("#fin_emissao").val('');
  			$("#fin_descricao").val('');
  			$("#fin_vencimento").val('');
  			$("#fin_descricao").val('');
  			$("#id_rh_modelo_sintetico").val('');
  			$("#vl_base").val('');
  			$("#referencia").val('');
  			$(".referencia-hide").hide();
  			$(".referencia").empty();
			}
    });

	
	$('body').on('click', 'a.tk-link', function(evt){
        evt.preventDefault();
        var id = $(this).attr("value");
        var tipoModelo = $(this).attr("tipoModelo");
        $("#id_agrupador_financeiro").val(id);
        $("#tipoModelo").val(tipoModelo);
        $( ".datepicker" ).datepicker();
        $('.dialog_tk').dialog('open');
    });

	$('body').on('click', 'a.edit_tk', function(evt){
 		evt.preventDefault();
        var id = $(this).attr("value");
        
        $( ".datepicker" ).datepicker();
        $("#valueDelete").html(id);
        $.ajax({
    		type : "POST",
    		url : '/rh/referencia-financeiro-modelo/get',
    		data : {fin_id : id},
    		success : function(data){
    			$("#fin_id").val(data.fin_id);
    			$("#id_rh_modelo_sintetico").val(data.id_rh_modelo_sintetico);
    			$("#vl_base").val(data.vl_base);
    			$("#referencia").val(data.referencia);
    		}
    	});
    	$.ajax({
    		type : "POST",
    		url : '/financial/financial/get',
    		data : {fin_id : id},
    		success : function(data){
    			$("#fin_id").val(data['fin_id']);
    			$("#fin_valor").val(data['fin_valor']);
    			$("#id_agrupador_financeiro").val(data['id_agrupador_financeiro']);
    			$("#fin_competencia").val(data['fin_competencia']);
    			$("#fin_emissao").val(data['fin_emissao']);
    			$("#fin_descricao").val(data['fin_descricao']);
    			$("#fin_vencimento").val(data['fin_vencimento']);
    		}
    	});
        $('.dialog_tk').dialog('open');
	});
	 
	$('body').on('click', 'a.deleteMoldal', function(evt){
		evt.preventDefault();
		var id = $(this).attr("value");
		$("#valueDelete").prepend(id);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-darkorange',
            resizable: true,
            title: "Excluir Ticket",
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
                    		  evt.preventDefault();
                    			$.ajax({
                    	            type: "POST",
                    	            url: '/rh/folha-de-pagamento/desativar-ticket',
                    	            data: { fin_id: id},
                    	            success: function(data){
                    	            	console.log(data.type);
                    	            	if (data.type == true){
                    	            		alert('Registro Excluído Com Sucesso');
                    	            		location.reload();	
                    	            	}else{
                    	            		alert("Erro ao excluir o Ticket "+id);
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
	
	$('#dialog_migrar').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Migrar Tks",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'gree btn',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("click", "#migra-tk", function(e){
		e.preventDefault();
    	$('#dialog_migrar').dialog('open');
	});
	
	$('body').on("click", ".id-migrar", function(){
		var tss = $(this).attr('data-tss');
		var idTk = $(this).attr('data-idTk');
    	$('#tss_migra').val(tss);
    	$('#idTk_migra').val(idTk);
    	$('#dialog_migra_cadastro').dialog('open');
    	$('#dialog_migra_cadastro').dialog({
            title: "Migrar Tk "+ idTk,
    	});
    	
    	$('#dialog_migrar').dialog('close');
	});

	$('#dialog_migra_cadastro').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        width: 650,
        height: 200,
        autoOpen: false,
        open: function(){
        	$( "#fin_descricao_migra" ).autocomplete({
        		source: "/rh/folha-de-pagamento/autocomplete-modelo/tipo/1",
        		minLength: 2,
        		select: function( event, ui ) {
        			$(".referencia").empty();
        			$(".referencia-hide").show();
        		    $("#id_rh_modelo_sintetico_migra").val(ui.item.id);
        		    $(".referencia").append(ui.item.referencia);
        		},
        		search: function( event, ui ) {
        		}
        	});
        },
        buttons: [
                  {
	                 'class' : 'red btn',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                  
	                  $('#dialog_migrar').dialog('open');
	                  $('#tss_migra').val('');
	              	  $('#idTk_migra').val('');
	              	  $("#id_rh_modelo_sintetico_migra").val('');
	              	  $( "#fin_descricao_migra" ).val('');
	              	  $("#referencia_migra").val('');
	              	  $(".referencia-hide").hide();
	                }
                  },
                  {
 	                 'class' : 'blue btn',
 	                 "text" : "Salvar",
 	                click: function() {
 	                	if($( "#fin_descricao_migra" ).val() != ''){
 	                		$.ajax({
 	                			type : "POST",
 	                			url : "/rh/folha-de-pagamento/migra",
 	                			data : {
 	                				fin_descricao : $( "#fin_descricao_migra" ).val(),
 	                				fin_id : $('#idTk_migra').val(),
 	                				id_agrupador_financeiro : $('#tss_migra').val(),
 	                				referencia : $("#referencia_migra").val(),
 	                				id_rh_modelo_sintetico :  $("#id_rh_modelo_sintetico_migra").val()
 	                			},
 	                			success : function(data){
 	                				if(data.success = true){
 	                					alert("Ticket migrado com sucesso.");
 	                					location.reload();
 	                				} else {
 	                					alert("Erro ao migrar esta TK");
 	                				}
 	                			}
 	                		});
 	                	} else {
 	                		alert('O Modelo Sintetico não pode estar vazio.');
 	                	}
 	                }
                   }
          ],
          close: function(){
          }
    });
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
	
});


$.fn.GridItens = function(id){
	$.ajax({
		type : "POST",
		url : "/rh/folha-de-pagamento/grid-ticket-ajax",
		data : {
			id_agrupador_financeiro : id
		},
		success : function(data){
			$('.grid-itens-'+id).html(data);
		}
	});
};

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
                	dt_competencia: {
                        required: true
                    },
                    descricao:{
                    	required: true
                    },
                    nome_empresa:{
                    	required: true
                    }
                },
                messages: { // custom messages for radio buttons and checkboxes
                    descricao: {
                        required: "Campo obrigatório."
                    },
                    nome_empresa: {
                    	required: "Campo obrigatório."
                    },
                    dt_competencia: {
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
                    App.scrollTo($('.portlet-title'));
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
                    App.scrollTo($('.portlet-title'));
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

                    App.scrollTo($('.portlet-title'));
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
        }

    };

}();