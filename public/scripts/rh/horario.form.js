$(document).ready(function(){
    $.fn.editable.defaults.inputclass = 'm-wrap';
    $.fn.editable.defaults.url = '/post';
    $.fn.editable.defaults.emptytext = '-------';
    $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
    $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
    
    $("input#tolerancia_extra").setMask({mask : '9999'});
    gridHorarioFuncionario();

    //-------------------------- Horario de entrada 1 --------------------------------
    $('.editable.entrada1').editable({
        format: 'hh:ii',
        url: '/rh/config-horario/form',
        send: 'auto',
        inputclass: 'm-wrap hora',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_config_horario = $(this).attr('data-pk');
            params.entrada1			     = params.value;
            params.id_horario        = $('#id_horario').val();
            params.semana		     = $(this).attr('data-semana');
            return params;
        },
        success: function(response, newValue) {
        	if(response.success){
        		$(this).parent().parent().find('a').attr('data-pk', response .id.id_config_horario);
        	}
        },
        display: function(value) {
            $(this).text(value);
            calcTime($(this).attr('data-pk'))
          } 
    });
    
    //-------------------------- Horario de saída 1 --------------------------------
    $('.editable.saida1').editable({
        format: 'hh:ii',
        url: '/rh/config-horario/form',
        send: 'auto',
        inputclass: 'm-wrap hora',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_config_horario = $(this).attr('data-pk');
            params.saida1  		     = params.value;
            params.id_horario        = $('#id_horario').val();
            params.semana		     = $(this).attr('data-semana');
            return params;
        },
        success: function(response, newValue) {
        	if(response.success){
        		$(this).parent().parent().find('a').attr('data-pk', response .id.id_config_horario);
        	}
        },
        display: function(value) {
            $(this).text(value);
            calcTime($(this).attr('data-pk'))
          } 
    });
    
    //-------------------------- HOrario de entrada 2 --------------------------------
    $('.editable.entrada2').editable({
        format: 'hh:ii',
        url: '/rh/config-horario/form',
        send: 'auto',
        inputclass: 'm-wrap hora',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_config_horario = $(this).attr('data-pk');
            params.entrada2 	     = params.value;
            params.id_horario        = $('#id_horario').val();
            params.semana		     = $(this).attr('data-semana');
            return params;
        },
        success: function(response, newValue) {
        	if(response.success){
        		$(this).parent().parent().find('a').attr('data-pk', response .id.id_config_horario);
        	}
        },
        display: function(value) {
            $(this).text(value);
            calcTime($(this).attr('data-pk'))
          } 
    });

    //-------------------------- horario de saída 2 --------------------------------
    $('.editable.saida2').editable({
        format: 'hh:ii',
        url: '/rh/config-horario/form',
        send: 'auto',
        inputclass: 'm-wrap hora',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_config_horario = $(this).attr('data-pk');
            params.saida2		     = params.value;
            params.id_horario        = $('#id_horario').val();
            params.semana		     = $(this).attr('data-semana');
            return params;
        },
        success: function(response, newValue) {
        	if(typeof response != "undefined" && response.success){
        		$(this).parent().parent().find('a').attr('data-pk', response .id.id_config_horario);
        	}
        },
        display: function(value) {
            $(this).text(value);
            calcTime($(this).attr('data-pk'))
          } 
    });
    
    //-------------------------- Tolerância de extra --------------------------------
    $('.editable.tolerancia_extra').editable({
        url: '/rh/config-horario/form',
        send: 'auto',
        inputclass: 'm-wrap toleracia',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_config_horario = $(this).attr('data-pk');
            params.tolerancia_extra  = params.value;
            params.id_horario        = $('#id_horario').val();
            params.semana		     = $(this).attr('data-semana');
            return params;
        },
        success: function(response, newValue) {
        	if(response.success){
        		$(this).parent().parent().find('a').attr('data-pk', response .id.id_config_horario);
        	}
        }
    });

    //-------------------------- Tolerância de falta --------------------------------
    $('.editable.tolerancia_falta').editable({
        url: '/rh/config-horario/form',
        send: 'auto',
        inputclass: 'm-wrap toleracia',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Este campo é obrigatório';
            }
        },
        params: function(params) {
            //originally params contain pk, name and value
            params.id_config_horario = $(this).attr('data-pk');
            params.tolerancia_falta  = params.value;
            params.id_horario        = $('#id_horario').val();
            params.semana		     = $(this).attr('data-semana');
            return params;
        },
        success: function(response, newValue) {
        	if(response.success){
        		$(this).parent().parent().find('a').attr('data-pk', response .id.id_config_horario);
        	}
        }
    });
    
    //------------------------editando o fechamento-----------------------------------
    $('.editable.fechamento').editable({
        format: 'hh:ii',
        url: '/rh/config-horario/form',
        send: 'auto',
        inputclass: 'm-wrap hora',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_config_horario = $(this).attr('data-pk');
            params.fechamento        = params.value;
            params.id_horario        = $('#id_horario').val();
            params.semana		     = $(this).attr('data-semana');
            return params;
        },
        success: function(response, newValue) {
        	console.log(response);
        	if(typeof response != "undefined" && response.success){
        		$(this).parent().parent().find('a').attr('data-pk', response .id.id_config_horario);
        	}
        }
    });
    
    //-----------------------editando o almoço livre----------------------------------------
    $('body').on('click', '.editable.almoco_livre', function(){
    	var almoco_livre = 0;
    	var $this = $(this);
    	if($(this).attr('data-value') == 0){
    		almoco_livre = 1;
    	}
    	$.post('/rh/config-horario/form',
    			{id_config_horario:$(this).attr('data-pk'), id_horario:$('#id_horario').val(), semana:$(this).attr('data-semana'), almoco_livre:almoco_livre},
    			function(data){
    				if(data.success){
    					$this.attr('data-value', almoco_livre);
    					if(almoco_livre == 1){
    						$this.text('Sim');
    					}else{
    						$this.text('Não');
    					}
    					$this.parent().parent().find('a').attr('data-pk', data .id.id_config_horario);
    				}
    			}
    	);
	});
    
    //-----------------------editando o compensado----------------------------------------
    $('body').on('click', '.editable.compensado', function(){
    	var compensado = 0;
    	var $this = $(this);
    	if($(this).attr('data-value') == 0){
    		compensado = 1;
    	}
    	$.post('/rh/config-horario/form',
    			{id_config_horario:$(this).attr('data-pk'), id_horario:$('#id_horario').val(), semana:$(this).attr('data-semana'), compensado:compensado},
    			function(data){
    				if(data.success){
    					$this.attr('data-value', compensado);
    					if(compensado == 1){
    						$this.text('Sim');
    					}else{
    						$this.text('Não');
    					}
    					$this.parent().parent().find('a').attr('data-pk', data .id.id_config_horario);
    				}
    			}
    	);
	});

    // ------------------- mascara do horario --------------------------
    $('body').on("click", "input.hora", function(){
		$("input.hora").setMask({mask : '99:99'});
    });
    
    //-------------------- adicionando o plugin timepicker no campo horario ----------------------------
    $(' .editable.entrada1, .editable.saida1, .editable.entrada2,  .editable.saida2, .editable.fechamento').on('shown', function(e, editable) {
    	$("input.hora").setMask({mask : '99:99'});
    	$('input.hora').timepicker({
            minuteStep: 1,
            showInputs: false,
            disableFocus: true,
            showSeconds: false,
            showMeridian: false,
            defaultTime: false
        });
    });
    
    $('body').on("focus", "input.tolerancia", function(){
		$("input.tolerancia").setMask({mask : '999'});
    });
    
    $('body').on("focus", "input.almoco_livre", function(){
		$("input.almoco_livre").setMask({mask : '999'});
    });
    
    //--------------------------------dialog funcionario-------------------------------------
    $('body').on('click', 'a.novo-funcionario', function(e){
    	e.preventDefault();
    	$('#dialog-funcionario').dialog({
            modal: true,
            title: 'Funcionário',
            dialogClass: 'ui-dialog-darkorange',
            resizable: true,
            width: 370,
            height: 276,
            open: function(){
            	var date1 = $( "#data").datepicker({
          		  defaultDate: "+1w",
          	      changeMonth: true,
          	      autoclose: true,
                  format: "dd/mm/yyyy",
                  language: "pt-BR",
                  onRender: function(date) {
                	    return date.valueOf();
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
                        	  if($('#id_funcionario').val() == ''){
                        		  alert('Escolha o funcionário');
                        		  return false;
                        	  }
                        	  if($('#data').val() == ''){
                        		  alert('Selecione uma data');
                        		  return false;
                        	  }
                    		  $.ajax({
                    			  type : "POST",
                    			  url : "/rh/horario-funcionario/form",
                    			  data : {id_rh_funcionario: $('#id_funcionario').val(), data:$('#data').val(), id_horario:$('#id_horario').val(), id_horario_funcionario:$('#id_horario_funcionario').val()},
                    			  success : function(data){
                    				  if(data.success == true){
                    					  gridHorarioFuncionario();
                    					  $('#dialog-funcionario').dialog('close');
                    				  } else{
                    					  alert('Contate o administrador.');
                    				  }
                    			  }
                    		  });
                        }
                }
              ],
              close: function(){
    	        	$("#id_funcionario").val('');
    	  			$("#data").val('');
    	  			$('#id_horario_funcionario').val('');
    			}
        });
    	
    }); 
    //----------------------------Editar funcionário--------------------------------
    $('body').on('click', 'a.editar-funcionario', function(e){
    	e.preventDefault();
    	var id_horario_funcionario = $(this).attr('value');
    	$('#id_horario_funcionario').val(id_horario_funcionario);
    	$.get('/rh/horario-funcionario/get/id_horario_funcionario/'+id_horario_funcionario, function(data){
    		$("#id_funcionario").val(data.id_rh_funcionario);
  			$("#data").val(data.data);
    	});
    	$('a.novo-funcionario').trigger('click');
    	
    });
    
    $('body').on('click', 'a.delete-funcionario', function(e){
    	e.preventDefault();
    	var id_horario_funcionario = $(this).attr('value');
    	$('div#dialog-delete-func').dialog({
            modal: true,
            title: 'Excluir funcionário',
            dialogClass: 'ui-dialog-darkorange',
            resizable: true,
            width: 370,
            height: 160,
            buttons: [
                      {
                         'class' : 'btn',
                         "text" : "Cancelar",
                         click: function() {
                        	 $(this).dialog('close');              
                         }
                      },
                      {
                         'class' : 'btn red',
                         "text" : "Excluir",
                          click: function() {
                        	  $.get('/rh/horario-funcionario/delete/id_horario_funcionario/'+id_horario_funcionario, function(data){
                        		  console.log(data);
                        		  console.log(data[0].type == "alert");
                        		  if(data[0].type == "success"){
                        			  alert('Registro removido com sucesso.');
                        			  gridHorarioFuncionario();
                        		  }else{
                        			  alert('não foi possivel excluir este registro');
                        		  }
                        	  });
                        	  $(this).dialog('close');  
                        }
                }
              ],
        });
        	
    	
    });
    
    //-----------------------------Duplicar Semana---------------------------
    $('body').on('click','button.duplicar-semana', function(e){
    	e.preventDefault();
    	$('div#dialog-semana').dialog({
            modal: true,
            title: 'Duplicar semana',
            dialogClass: 'ui-dialog-darkorange',
            resizable: true,
            width: 350,
            height: 300,
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
                         "text" : "Duplicar",
                          click: function() {
                        	  var semana_de = $('#semana_de').val();
                        	  var id_horario = $('#id_horario').val();
                        	  var semana_para = [];
                        	  $('button.semana_para.blue').each(function(key, value){
                        		  semana_para[key] = $(this).attr('value');
                    		  });
                        	  console.log(semana_para);
                        	  $.post('/rh/config-horario/duplicar/id_horario/'+id_horario+'/semana_de/'+semana_de,{semana_para:semana_para}, function(data){
                        		  if(data.success == true){
                        			  alert('Registro duplicado com sucesso.');
                        			  location.reload();
                        		  }else{
                        			  alert('não foi possivel duplicar este registro');
                        		  }
                        	  });
                        	  $(this).dialog('close');  
                        }
                }
              ],
        });
    });
    
    $('#semana_de').change(function(){
    	var semana_de = $(this).val();console.log(semana_de);
    	$('button.semana_para').show();
    	$('button.semana_para[value="'+semana_de+'"]').hide().removeClass('blue').removeClass('active');
    });
    
    $('body').on('click', 'button.semana_para', function(e){
    	e.preventDefault();
    	$(this).toggleClass('blue').toggleClass('active');
    });
    

    //-------------------------- Configuração de hora extra --------------------------------
    $('.editable.extra').editable({
        url: '/rh/config-extra/form',
        send: 'auto',
        inputclass: 'm-wrap extra_porcentagem',
        params: function(params) {
            //originally params contain pk, name and value
            params.id_config_extra   		= $(this).attr('data-pk');
            params.porcentagem_desconto		= params.value;
            params.id_horario        		= $('#id_horario').val();
            params.hora_inicio		     	= $(this).attr('data-horario_inicio');
            params.hora_fim		     		= $(this).attr('data-horario_fim');
            params.tipo_dia		     		= $(this).attr('data-tipo_dia');console.log(params);
            return params;
        },
        success: function(response, newValue) {
        	if(response.success){
        		$(this).attr('data-pk', response .id.id_config_horario);
        	}
        },
        display: function(value) {
        	if($.isNumeric(value)){
        		$(this).text(value+"%");
        	}
            
          } 
    });
    $('.editable.extra').on('shown', function(e, editable) {
    	$("input.extra_porcentagem").setMask({mask : '999'});
    });
    $('a.banco-hora').tooltip();
    $('body').on('click', 'a.banco-hora', function(e){
    	var siblings = $(this).siblings('a');
    	console.log(siblings);
    	var post_data = {id_config_extra: siblings.attr('data-pk'), 
    					porcentagem_desconto: null, 
    					id_horario: $('#id_horario').val(), 
    					hora_inicio: siblings.attr('data-horario_inicio'),
    					hora_fim: siblings.attr('data-horario_fim'),
    					tipo_dia: siblings.attr('data-tipo_dia'),
    					banco_horas: 1}
    	$.post('/rh/config-extra/form', post_data, function(data){
    		if(data.success){
    			siblings.text('Banco de horas');
    			siblings.attr('data-pk', data.id.id_config_extra);
    		}
    	});
    });
    
});

function calcTime(idHorarioConfig){
	if(!idHorarioConfig){
		return;
	}
	var entrada1 	= $.trim($('.editable.entrada1[data-pk="'+idHorarioConfig+'"]').text());
	var entrada2 	= $.trim($('.editable.entrada2[data-pk="'+idHorarioConfig+'"]').text());
	var saida1 		= $.trim($('.editable.saida1[data-pk="'+idHorarioConfig+'"]').text());
	var saida2 		= $.trim($('.editable.saida2[data-pk="'+idHorarioConfig+'"]').text());
	var result 		= calculaHoras(verificarTime(entrada1), verificarTime(saida1), verificarTime(entrada2), verificarTime(saida2));
	$('.editable.carga[data-pk="'+idHorarioConfig+'"]').text(result != undefined? result: '-------');
}
function verificarTime(time){
	if(time == "-------" || time == ""){
		return null;
	}
	return time;
}

function gridHorarioFuncionario(){
	var id_horario = $('#id_horario').val();console.log(id_horario);
	if(!id_horario){
		return;
	}
	$.get('/rh/horario-funcionario/grid-funcionario/id_horario/'+id_horario, function(data){
		$('#grid-funcionario-horario').html(data);
	});
}