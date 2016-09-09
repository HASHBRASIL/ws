$(document).on("hover", ".tooltips",  function(){
		$(this).tooltip();
});
$(document).ready(function(even) {

    $.fn.editable.defaults.inputclass = 'm-wrap';
    $.fn.editable.defaults.url = '/post';
    $.fn.editable.defaults.emptytext = '-------';
    $.fn.editableform.buttons = '<button type="submit" class="btn blue editable-submit"><i class="icon-ok"></i></button>';
    $.fn.editableform.buttons += '<button type="button" class="btn editable-cancel"><i class="icon-remove"></i></button>';
	//$.fn.editable.defaults.mode = 'inline';
	
	var date1 = $( "#data").datepicker({
		  defaultDate: "+1w",
	      changeMonth: true,
	      minViewMode: 1,
	      autoclose: true,
	      format: "MM/yyyy",
	      language: "pt-BR",
	      onRender: function(date) {
	    	    return date.valueOf();
	    	  }
	});

	$('.date').setMask();

	$("#id_funcionario").select2();
	$("#ponto").select2();
	$("#id_rh_justificacao_ponto").select2();
	
	$('body').on("click", ".buscar", function(e){
		e.preventDefault();

		if($("#data").val() == ""){
			alert('Campo período é obrigatório.');
			return false;
		}
//		if($("#ponto").val() == ""){
//			alert('Campo ponto é obrigatório.');
//			return false;
//		}
		if($("#id_funcionario").val() == ""){
			alert('Campo funcionario é obrigatório.');
			return false;
		}
		
		$(this).GridItens();
	});

	$('body').on("change", "#id_funcionario", function(e){
		e.preventDefault();
		if($("#data").val() == ""){
			return false;
		}
		if($("#id_funcionario").val() == ""){
			return false;
		}
		
		$(this).GridItens();
	});
	
	
	$('body').on("click", ".imprimir", function(e){
		e.preventDefault();

		if($("#data").val() == ""){
			alert('Campo período é obrigatório.');
			return false;
		}
		if($("#id_funcionario").val() == ""){
			alert('Campo funcionario é obrigatório.');
			return false;
		}
		
		var data_periodo = getDataPeriodo();
		data_periodo = data_periodo.replace("/","-");
		data_periodo = data_periodo.replace("/","-");
		
		window.open(
				"/rh/ponto/relatorio/ponto/"+$("#ponto").val()+"/dataInicial/"+data_periodo+"/funcionario/"+$("#id_funcionario").val(),
				  '_blank'
				);
	});

	$('body').on("click", ".editable-submit", function(){
			var valor = $(".hora").val();
			if (valor == "") {
				$(".id_rh_justificacao_ponto").show();
			}else{
				$(".id_rh_justificacao_ponto").hide();
			}
	});

	$('body').on("focus", "#horas input", function(){
			$("#horas input").setMask({mask : '99:99:99'});
	});

	$('.diolog_justificacao').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        resizable: false,
        title: "Motivo",
        width: 450,
        height: 300,
        autoOpen: false,
        draggable: true,
        open: function() {
            $(this).css('overflow', 'hidden');
        },
        buttons: [
                  {
	                 'class' : 'green btn',
	                 "text" : "Salvar",
	                click: function() {

	            		if($("#descricao").val() == ""){
	            			alert('Campo descrição é obrigatório.');
	            			return false;
	            		}
	            		
	            		if($('#id_rh_justificacao_ponto').val() == "" && $('div.control-group.id_rh_justificacao_ponto').is(':visible')){
	            			alert('Selecione a justificativa é obrigatório.');
	            			return false;
	            		}

            		   $('#horas a').editable('submit', {
            		       url: '/rh/dados-ponto/form',
            		       ajaxOptions: {
            		           dataType: 'json' //assuming json response
            		       },
            		       data: {
            		               id_rh_dados_ponto:$('#id_rh_dados_ponto').val(), 
            		               hora:$('#value_hora').val(), 
            		               descricao:$('#descricao').val(), 
            		               data:$('#data_select').val(),
            		               id_rh_funcionario:$('#id_funcionario').val(),
            		               id_rh_justificacao_ponto: $('#id_rh_justificacao_ponto').val()
            		             },
            		       success: function(data, config) {
                               var eq_hora = $('#eq_element').val();
                               var descricao = $('#descricao').val();
                               var span_texto;
            		           if(data.success == true){
                                   var id_rh_justificacao_ponto = $('#id_rh_justificacao_ponto').val();
                                   if(id_rh_justificacao_ponto != ""){
                                       span_texto = $('#id_rh_justificacao_ponto option[value="'+id_rh_justificacao_ponto+'"]').text();
                                       $('#horas a').eq(eq_hora).text(span_texto);
                                       $('body').GridItens();
                                   }
                                   $(this).eq(eq_hora).attr('data-pk', data.id.id_rh_dados_ponto);
                                   if($('#horas a').eq(eq_hora).parent().find('span').length == 0){
                                       $('#horas a').eq(eq_hora).parent().append('<span class="badge badge-important tooltips" data-placement="top" title="" style="float: right;" data-original-title="'+descricao+'">!</span>');
                                       $('.tooltips').tooltip();
                                   }else{
                                       $('#horas a').eq(eq_hora).parent().find('span').attr('data-original-title', descricao);
                                       $('.tooltips').tooltip();
                                   }
                                   $(this).GridItens();
                                   $('.diolog_justificacao').dialog( "close" );
            		           }else{
            		               alert('não foi possivel salvar o dado, tente novamente mais tarde!');
            		           }
            		       },
            		       error: function(errors) {
            		           alert('não foi possivel salvar o dado, tente novamente mais tarde!');
            		           $('.diolog_justificacao').dialog( "close" );
            		       }
            		   });
	            		
	                }
                  }
          ],
          close: function(){
              $('#id_rh_dados_ponto').val('');
              $('#value_hora').val('');
              $('#descricao').val('');
              $('#data_select').val('');
              $('#eq_element').val('');
              $('#id_rh_justificacao_ponto').val('');
              $("#id_rh_justificacao_ponto").select2();
              $(this).GridItens();
          }
    });
	$( ".diolog_justificacao" ).dialog({ closeOnEscape: false });
	
	$('body').on('click', '#duplicado', function(e){
	    e.preventDefault();
        $('#dialog_duplicado').dialog({
            modal: true,
            autoOpen: false,
            dialogClass: 'ui-dialog-darkorange',
            resizable: false,
            title: "Duplicados",
            width: 450,
            height: 'auto',
            maxHeight: 450,
              close: function(){
                  $('#dialog_duplicado').html('');
                  getCountDuplicado();
              }
        });
	    $.post('/rh/ponto/grid-duplicado', 
                {data_inicial:getDataPeriodo(),id_funcionario:$('#id_funcionario').val()},
                function(data){
                    if(typeof data == "object" ){
                        alert('Não possui datas duplicadas.');
                        $('#dialog_duplicado').dialog('close');
                    }else{
                        $('#dialog_duplicado').html(data);
                        $('#dialog_duplicado').dialog('open');
                    }
                });
	    
	});
	
	$('body').on('click', 'a.btn-duplicado', function(e){
	    e.preventDefault();
	    var id_rh_dados_ponto = $(this).attr('data-id');
	    var $this = $(this);
        $.post('/rh/dados-ponto/form', 
                {
                    id_rh_dados_ponto:id_rh_dados_ponto,
                    duplicado: 3
                },
                function(data){
                    if(data.success == true){
                        alert('Dado aprovado com sucesso.');
                        if($('#dialog_duplicado tbody tr').length == 1){
                            $('#dialog_duplicado').dialog('close');
                            $(this).GridItens();
                        }else{
                            $this.parent().parent().remove();
                            $(this).GridItens();
                        }
                    }else{
                        alert('não foi possivel aprovar o dado, tente novamente mais tarde!');
                    }
                });
        
	});
	
	//-----------------------------traz a grade de horario ---------------------------------
	$('body').on('click', 'button.grade-horario', function(e){
		e.preventDefault();
	    $.post('/rh/horario/grid-horario', 
	            {data_inicial:getDataPeriodo(),id_funcionario:$('#id_funcionario').val()},
	            function(data){
	            	if(typeof data == 'object'){
	            		alert(data.message);
	            	}else{
	            		$('#dialog-horario').html(data);
	            		$('#dialog-horario').dialog('open');
	            	}
	            });
        $('#dialog-horario').dialog({
            modal: true,
            dialogClass: 'ui-dialog-darkorange',
            resizable: false,
            title: "Horario Padrão",
            width: 450,
            height: 'auto',
            maxHeight: 450,
            autoOpen: false,
	          close: function(){
	              $('#dialog-horario').html('');
	          }
        
        });
		
	});
	
	//----------------------------Ordena os registro de entrada e saida------------------------------
	$('body').on('click', 'i.icon-chevron-right.icon-position', function(){
		var data = $(this).siblings('a').attr('data-data');
		var tr_parent = $(this).parent().parent().find('td.position[data-data="'+data+'"]');
		var indexAnterior = $(this).parent().index('td.position[data-data="'+data+'"]');
		var indexProximo = indexAnterior +1;console.log(indexProximo);
		if(indexAnterior != 3){console.log('aki');
			var anterior = $(this).parent().html();
			var proximo = $(this).parent().parent().find('td.position').eq(indexProximo).html();
			$(this).parent().parent().find('td.position').eq(indexProximo).html(anterior);
			$(this).parent().html(proximo);
			
			tr_parent.each(function(index, element){
				$(element).find('a').attr('data-posicao', index);
			});
			salvarPosition(tr_parent);
		}
	});
	$('body').on('click', 'i.icon-chevron-left.icon-position', function(){
		var data = $(this).siblings('a').attr('data-data');
		var tr_parent = $(this).parent().parent().find('td.position[data-data="'+data+'"]');
		var indexAtual = $(this).parent().index('td.position[data-data="'+data+'"]');
		var indexProximo = indexAtual -1;
		if(indexAtual != 0){
			var anterior = $(this).parent().html();
			var proximo = $(this).parent().parent().find('td.position').eq(indexProximo).html();

			$(this).parent().parent().find('td.position').eq(indexProximo).html(anterior);
			$(this).parent().html(proximo);

			tr_parent.each(function(index, element){
				$(element).find('a').attr('data-posicao', index);
			});
			salvarPosition(tr_parent);
		}
	});
});

function salvarPosition(tr){
	var data = [];
	var values = tr.map(function () {
	    return $(this).find('a').attr('data-pk');
	}).get();
	$.post('/rh/dados-ponto/ordenar', {'posicao[]': values}, function(data){
		if(data.success == false){
			alert(data.message);
			$(this).GridItens();
		}
	});
}

$.fn.GridItens = function(){
    $('#duplicado').show();
    getCountDuplicado();
	$.ajax({
		type : "POST",
		url : "/rh/ponto/grid-folha-de-ponto-ajax",
		data : {
			dataInicial : getDataPeriodo(),
			ponto : $("#pontoId").val(),
			funcionario : $("#id_funcionario").val()
		},
		success : function(data){
			if(typeof data == 'object'){
				alert(data.message);
				return;
			}
			$('#grid').html(data);
		    
		    $('#horas a').editable({
		        type: 'text',
		        format: 'hh:ii',
		        url: '/rh/dados-ponto/form',
		        send: 'never',
		        unsavedclass: null,
		        //showbuttons: true,
		        emptyclass: null,
		        savenochange: true,
		        emptytext: '-------',
		        inputclass: 'm-wrap hora',
		        pk: $(this).attr('data-pk'),
		        validate: function(value) {
		            hrs = (value.substring(0,2));  
		            min = (value.substring(3,5));  

		            if ((hrs < 00 ) || (hrs > 23) || ( min < 00) ||( min > 59)){
		                console.log('validate');
		               return "Hora invalida!";  
		            }  
		            
		        }
		    });
		    $(".tooltips").tooltip();
		    
		    $('#horas a').on('save.editable-submit', function(e, params){
		        $('#id_rh_dados_ponto').val($(this).attr('data-pk'));
		        $('#data_select').val($(this).attr('data-data'));
		        $('#value_hora').val(params.newValue);
		        $('#eq_element').val($('#horas a').index($(this)));
		        $('.diolog_justificacao').dialog('open');
		    });
		}
	});
};


function checkTime(i) {
    if (i<10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}

function getCountDuplicado(){
    $.post('/rh/ponto/count-duplicado', 
            {data_inicial:getDataPeriodo(),id_funcionario:$('#id_funcionario').val()},
            function(data){
                if($.isNumeric(data.count) ){
                    $('#duplicado span.count').text('('+data.count+')');
                }else{
                	$('#duplicado span.count').text('');
                }
            });
}

function getDataPeriodo(){
	var data_inicial	= $('#data').datepicker('getDate');
	var month_inicial 	= data_inicial.getMonth()+1;
	return checkTime(data_inicial.getDate())+'/'+checkTime(month_inicial)+'/'+data_inicial.getFullYear();
}
