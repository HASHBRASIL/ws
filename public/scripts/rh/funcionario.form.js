$(document).ready(function(even) {
	
	FormWizard.init();
	
	$('body').on("click","button.salvar", function(){	
		if($('#id_empresa').val() == ""){
			alert('O campo funcionario é obrigatório.');
			return false;
		}

		if($('#dt_nascimento').val() == ""){
			alert('O campo data de nascimento é obrigatório.');
			return false;
		}

		if($('#ctps').val() == ""){
			alert('O campo ctps é obrigatório.');
			return false;
		}

		if($('#pis').val() == ""){
			alert('O campo pis é obrigatório.');
			return false;
		}

		if($('#dt_admissao').val() == ""){
			alert('O campo data de admissão é obrigatório.');
			return false;
		}

		if($('#funcao_cbo').val() == ""){
			alert('O campo função é obrigatório.');
			return false;
		}

		if($('#salario').val() == ""){
			alert('O campo salario é obrigatório.');
			return false;
		}
	});
	$( "#funcao_cbo" ).autocomplete({
		source: "/rh/cbo/autocomplete",
		minLength: 2,
		select: function( event, ui ) {
		    $("#id_rh_cbo").val(ui.item.id);
		    $("#codigo_cbo").val(ui.item.codigo);
		},
		search: function( event, ui ) {
		}
	});
	
	$('.data').setMask({mask : '99/99/9999'});
	$('.rg').setMask({mask : '999.999.999.999.999', type : 'reverse'});
	$('.decimal').setMask(); 
	$('.horas').setMask({mask : '99:999', type : 'reverse'});
	var entrada = $('#entrada').val();
	var saida = $('#saida').val();
	var refeicao_entrada = $('#refeicao_entrada').val();
	var refeicao_saida = $('#refeicao_saida').val();
	$('.hora').setMask({mask : '99:99', type : 'reverse'});
    $('#hora_mensal, #hora_semanal').setMask({mask : '99999'});

    $('#entrada').val(entrada);
    $('#saida').val(saida);
    $('#refeicao_entrada').val(refeicao_entrada);
    $('#refeicao_saida').val(refeicao_saida);
	
	var entidade = $('#id_empresa').val();
	if(entidade != ''){
		$.ajax({
			type: "POST",
			url: '/sis/endereco/get-by-empresa/',
			data: {
				id_empresa: entidade
			},
			success: function(data){
				if(data[0].cep != null){					
					$('#endereco').val(data[0].nome_logradouro+' Nº '+data[0].numero);
					$('#bairro').val(data[0].bairro);
					$('#estado').val(data[0].uf_sigla);
					$('#cidade').val(data[0].cidade_nome);
					$('#cep').val(data[0].cep);

					$.ajax({
						type: "POST",
						url: '/empresa/empresa/get/',
						data: {
							id: entidade
						},
						success: function(data){
							
							var str = "Por gentileza, volte ao cadastro corporativo do funcionario.";
							
							if(data.nome_mae == null){
								str = str + '\n Nome da mãe é obrigatorio';
							}
							if(data.telefone1 == null){
								str = str + '\n Telefone é obrigatorio';
							}
							if(data.email_corporativo == null){
								str = str + '\n E-mail é obrigatorio';
							}
							if(data.estadual == null){
								str = str + '\n Rg é obrigatorio';
							}
							if(data.nome_mae == null || data.telefone1 == null || data.email_corporativo == null || data.estadual == null){
								alert(str);
								return false;
							}
							$('#telefone1').val(data.telefone1);
							$('#telefone2').val(data.telefone2);
							$('#telefone3').val(data.telefone3);
							$('#email').val(data.email_corporativo);
							$('#nome_mae').val(data.nome_mae);
							$('#nome_pai').val(data.nome_pai);
							$('#cpf').val(data.cnpj_cpf);
							$('#identidade').val(data.estadual);
							$('.entidade').show();
							$('.telefone').maskTelefone();
						}
					});
					
				}else{
					alert("Por gentileza, volte ao cadastro corporativo do funcionario.\nPreencha todo o endereço.");
					return false;
				}
			}
		});
	} else {
		$('.entidade').hide();
	}
	
	$("#id_empresa").change(function() {
		var entidade = $('#id_empresa').val();
		
		$.ajax({
			type: "POST",
			url: '/rh/funcionario/get-funcionario/',
			data: {
				id: entidade
			},
			success: function(data){
				if(data.ok == true){
					alert('Funcionario cadastrado em outro Workspace!');
					window.location.href = "/rh/funcionario/form/id_rh_funcionario/"+data.id_rh_funcionario;
				}else {
					
					if(entidade != ''){
						$.ajax({
							type: "POST",
							url: '/sis/endereco/get-by-empresa/',
							data: {
								id_empresa: entidade
							},
							success: function(data){
								if(data.success != false){					
									$('#endereco').val(data[0].nome_logradouro+' Nº '+data[0].numero);
									$('#bairro').val(data[0].bairro);
									$('#estado').val(data[0].uf_sigla);
									$('#cidade').val(data[0].cidade_nome);
									$('#cep').val(data[0].cep);

									$.ajax({
										type: "POST",
										url: '/empresa/empresa/get/',
										data: {
											id: entidade
										},
										success: function(data){
											
											var str = "Por gentileza, volte ao cadastro corporativo do funcionario.";
											
											if(data.nome_mae == null){
												str = str + '\n Nome da mãe é obrigatorio';
											}
											if(data.telefone1 == null){
												str = str + '\n Telefone é obrigatorio';
											}
											if(data.email_corporativo == null){
												str = str + '\n E-mail é obrigatorio';
											}
											if(data.estadual == null){
												str = str + '\n Rg é obrigatorio';
											}
											if(data.nome_mae == null || data.telefone1 == null || data.email_corporativo == null || data.estadual == null){
												alert(str);
												return false;
											}
											$('#telefone1').val(data.telefone1);
											$('#telefone2').val(data.telefone2);
											$('#telefone3').val(data.telefone3);
											$('#email').val(data.email_corporativo);
											$('#nome_mae').val(data.nome_mae);
											$('#nome_pai').val(data.nome_pai);
											$('#cpf').val(data.cnpj_cpf);
											$('#identidade').val(data.estadual);
											$('.entidade').show();
											$('.telefone').maskTelefone();
										}
									});
									
								}else{
									alert("Por gentileza, volte ao cadastro corporativo do funcionario.\nPreencha todo o endereço.");
									return false;
								}
							}
						});
					} else {
						$('.entidade').hide();
					}
				}
			}
		});
	});
	
	$('#dialog_tipo_admissao').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Tipo de Admissão",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on( "focus","input#id_rh_tipo_admissao", function(){
		$("#id_rh_tipo_admissao").blur();
    	$('#dialog_tipo_admissao').dialog('open');
	});
	$('body').delegate(".tipo-admissao", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_tipo_admissao").val(id);
    	$('#dialog_tipo_admissao').dialog('close');
	});
	
	$('#dialog_categoria').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Categoria",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
        	  $("#id_rh_categoria").focusout();
          }
    });
	
	$('body').on("focus", "input#id_rh_categoria", function(){
		$("#id_rh_categoria").blur();
    	$('#dialog_categoria').dialog('open');
	});
	
	$('body').delegate(".tipo-categoria", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_categoria").val(id);
    	$('#dialog_categoria').dialog('close');
	});

	$('#dialog_ocorrencia').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Ocorrência",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
        	  $("#id_rh_ocorrencia").focusout();
          }
    });
	
	$('body').on("focus", "input#id_rh_ocorrencia", function(){
		$("#id_rh_ocorrencia").blur();
    	$('#dialog_ocorrencia').dialog('open');
	});
	
	$('body').delegate(".tipo-ocorrencia", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_ocorrencia").val(id);
    	$('#dialog_ocorrencia').dialog('close');
	});

	$('#dialog_caged').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Caged",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("focus", "input#id_rh_caged", function(){
		$("#id_rh_caged").blur();
    	$('#dialog_caged').dialog('open');
	});
	
	$('body').delegate(".tipo-caged", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_caged").val(id);
    	$('#dialog_caged').dialog('close');
	});
	
	$('#dialog_vinculo').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Vinculo",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("focus", "input#id_rh_vinculo", function(){
		$("#id_rh_vinculo").blur();
    	$('#dialog_vinculo').dialog('open');
	});
	
	$('body').delegate(".tipo-vinculo", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_vinculo").val(id);
    	$('#dialog_vinculo').dialog('close');
	});
	
	$('#dialog_instrucao').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Instrução",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("focus", "input#id_rh_instrucao", function(){
		$("#id_rh_instrucao").blur();
    	$('#dialog_instrucao').dialog('open');
	});
	
	$('body').delegate(".tipo-instrucao", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_instrucao").val(id);
    	$('#dialog_instrucao').dialog('close');
	});
	
	$('#dialog_contrato').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Instrução",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("focus", "input#id_rh_contrato", function(){
		$("#id_rh_contrato").blur();
    	$('#dialog_contrato').dialog('open');
	});
	
	$('body').delegate(".tipo-contrato", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_contrato").val(id);
    	$('#dialog_contrato').dialog('close');
	});
	
	$('#dialog_escala').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Escala",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("focus", "input#id_rh_escala", function(){
		$("#id_rh_escala").blur();
    	$('#dialog_escala').dialog('open');
	});
	
	$('body').delegate(".tipo-escala", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_escala").val(id);
    	$('#dialog_escala').dialog('close');
	});
	
	$('#dialog_passagem').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Passagens",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("focus", "input#id_rh_passagem", function(){
		$("#id_rh_passagem").blur();
    	$('#dialog_passagem').dialog('open');
	});
	
	$('body').delegate(".tipo-passagem", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_passagem").val(id);
    	$('#dialog_passagem').dialog('close');
	});
	
	$('#dialog_local').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Locais",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("focus", "input#id_rh_local", function(){
		$("#id_rh_local").blur();
    	$('#dialog_local').dialog('open');
	});
	
	$('body').delegate(".tipo-local", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_local").val(id);
    	$('#dialog_local').dialog('close');
	});
	
	$('#dialog_sindicato').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: true,
        title: "Sindicatos",
        width: 650,
        height: 350,
        autoOpen: false,
        buttons: [
                  {
	                 'class' : 'btn gree',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  }
          ],
          close: function(){
          }
    });
	
	$('body').on("focus", "input#id_rh_sindicado", function(){
		$("#id_rh_sindicado").blur();
    	$('#dialog_sindicato').dialog('open');
	});
	
	$('body').delegate(".tipo-sindicato", "click", function(){
    	var id = $(this).attr('data-value');
    	$("#id_rh_sindicado").val(id);
    	$('#dialog_sindicato').dialog('close');
	});
});

//mascara do telefone se tiver mais que um local
$.fn.maskTelefone = function(){
	$this = $(this);
	$(this).focusin(function(){
	    $(this).on('keyup', function(e){
	        var telefone = $(this).val();
	    	if(telefone != ""){
	    		telefone = telefone.replace(/-/g, '').replace(/\(/g, '').replace(/\)/g, '').replace(/\+/g, '').replace(/\s/g, '');
		        console.log( telefone.length);
		    	if(telefone[0] == 0){
		    		$(this).setMask({mask:'9999 999 9999',autoTab: false});
		    	}else if(telefone.length <= 10){
		    		$(this).setMask({mask:'(99)9999-99999',autoTab: false});
		        }else if(telefone.length == 11){
		        	$(this).setMask({mask:'(99)99999-99999',autoTab: false});
		        }else if(telefone.length == 12){
		        	$(this).setMask({mask:'99(99)9999-99999',autoTab: false});
		        }else if(telefone.length == 13){
		        	$(this).setMask({mask:'99(99)99999-9999',autoTab: false});
		        }
	    		
	    	}
	    }); 
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

            form.validate({errorElement : 'span', // default input error message container
            	errorClass : 'help-inline', // default input error message class
            	focusInvalid : false, // do not focus the last invalid input
            	rules : {
            		id_empresa : {
            			required : true
            		},
            		dt_nascimento :{
            			required : true
            		},
            		ctps : {
            			required : true
            		},
            		pis : {
            			required : true
            		},
            		dt_admissao : {
            			required : true
            		},
            		funcao_cbo : {
            			required : true
            		},
            		salario : {
            			required : true
            		}
            	},
            	messages : { // custom messages for radio buttons and
            					// checkboxes
            		id_empresa : {
            			required : "Campo obrigatório."
            		},
            		dt_nascimento :{
            			required : "Campo obrigatório."
            		},
            		ctps : {
            			required : "Campo obrigatório."
            		},
            		pis : {
            			required : "Campo obrigatório."
            		},
            		dt_admissao : {
            			required : "Campo obrigatório."
            		},
            		funcao_cbo : {
            			required : "Campo obrigatório."
            		},
            		salario : {
            			required : "Campo obrigatório."
            		}
            	},

            	highlight : function(element) { // hightlight error inputs
            		$(element).closest('.help-inline').removeClass('ok'); // display
            																// OK
            																// icon
            		$(element).closest('.control-group').removeClass('success')
            				.addClass('error'); // set error class to the
            									// control group
            	},

            	success : function(label) {
            		label.addClass('valid') // mark the current input as valid
            								// and display OK icon
            		.closest('.control-group').removeClass('error').addClass(
            				'success'); // set success class to the control
            							// group
            	}

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