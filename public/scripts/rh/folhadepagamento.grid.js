$(document).ready(function(){

    $('.datepickerMonth').datepicker( {
  	  defaultDate: "+1w",
      changeMonth: true,
      minViewMode: 1,
      autoclose: true,
      format: "MM/yyyy",
      language: "pt-BR",
	 	});
	$('body').on('click', 'a.deleteMoldal', function(e){
        e.preventDefault();
        var id = $(this).attr("value");
        $("#valueDelete").prepend(id);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-darkorange',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir Registro",
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
                    		  window.location.href="/rh/folha-de-pagamento/delete/id_rh_folha_de_pagamento/"+id;
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
        
    });
	$('body').on('click', 'a.oletires', function(e){
        e.preventDefault();
        $('#dialog_olerite').dialog({
            modal: true,
            dialogClass: 'ui-dialog-darkorange',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Imprimir Olerites referente ao mês.",
            width: 450,
            height: 200,
            buttons: [
                      {
		                 'class' : 'red btn',
		                 "text" : "Cancelar",
		                click: function() {
		                  $( this ).dialog( "close" );
		                }
                      },
                      {
 		                 'class' : 'blue btn ',
 		                 "text" : "Imprimir",
 		                click: function() {
 		                	var data = $('.datepickerMonth#dt_competencia').datepicker('getDate');
 		                	data = data.toLocaleDateString();
 		                	data = data.replace("/", "-");
 		                	data = data.replace("/", "-");
 		                	window.open('/rh/folha-de-pagamento/olerites/data/'+data,'_blank');
 			                $( this ).dialog( "close" );
 		                }
                       }
              ],
              close: function(){
            	  
              }
        });
        
    });
	$('body').on('click', 'a.totalizacao', function(e){
        e.preventDefault();
        $('#dialog_relatorio_total').dialog({
            modal: true,
            dialogClass: 'ui-dialog-darkorange',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Imprimir a totalização referente ao mês.",
            width: 450,
            height: 200,
            buttons: [
                      {
		                 'class' : 'red btn',
		                 "text" : "Cancelar",
		                click: function() {
		                  $( this ).dialog( "close" );
		                }
                      },
                      {
 		                 'class' : 'blue btn ',
 		                 "text" : "Imprimir",
 		                click: function() {
 		                	var data = $('.datepickerMonth#dt_competencia_total').datepicker('getDate');
 		                	data = data.toLocaleDateString();
 		                	data = data.replace("/", "-");
 		                	data = data.replace("/", "-");
 		                	window.open('/rh/folha-de-pagamento/relatorio/data/'+data,'_blank');
 			                $( this ).dialog( "close" );
 		                }
                       }
              ],
              close: function(){
            	  
              }
        });
        
    });
	$('body').on('click', 'a.duplicarRegistro', function(e){
        e.preventDefault();
        var id = $(this).attr("value");
        var user = $(this).attr('valueEmpressa');
        $("#id_agrupador_financeiro").val(id);
        $("#userId").val(user);
        $( ".datepicker" ).datepicker({
            	  defaultDate: "+1w",
                  autoclose: true,
                  format: 'dd/mm/yyyy',
                  language: "pt-BR",
        });
        $('.datepickerMonth').datepicker( {
      	  defaultDate: "+1w",
          changeMonth: true,
          minViewMode: 1,
          autoclose: true,
          format: "MM/yyyy",
          language: "pt-BR",
   	 	});
        $('#dialogDuplicar').dialog({
            modal: true,
            dialogClass: 'ui-dialog-darkorange',
            resizable: true,
            title: "Duplicar Registro "+ id,
            width: 450,
            height: 'auto',
            open : function(){
            	$('input').blur();
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
		                 'class' : 'btn green',
		                 "text" : "Duplicar",
                    	  click: function() {
                    		  var mes = $('#fin_emissao').val();
                    		  if(mes != ''){
                    			  var data = $('.datepickerMonth#fin_emissao').datepicker('getDate');
                    			  fin_emissao = new Date(data);
                    			  var formSerializa = {id_agrupador_financeiro:$('#id_agrupador_financeiro').val(),userId:$('#userId').val(), fin_vencimento:$('#fin_vencimento').val(), fin_emissao:fin_emissao.toLocaleString()};
                    			  $.ajax({
                    				  type : "POST",
                    				  url : "/rh/folha-de-pagamento/validar-campos-ajax",
                    				  data : formSerializa,
                    				  success : function(data){
                    					  if(data.resposta == true){
                    						  $.ajax({
                    							  type : "POST",
                    							  url : "/rh/folha-de-pagamento/duplicar",
                    							  data : formSerializa,
                    							  success : function(data){
                    								  alert('Folha de pagamento duplicada com sucesso!');
                    								  table();
                    								  $('#dialogDuplicar').dialog('close');
                    							  }
                    						  });
                    						  return true;
                    					  } else if(data.resposta == false) {
                    						  $('.mes').addClass('error');
                    						  $("#msgValidate").append("<span class='help-block'>"+data.message+"</span>");
                    						  return false;
                    					  }
                    				  }
                    			  });
                    		  }else {
                    			  $('.mes').addClass('error');
                    			  $("#msgValidate").append("<span class='help-block'>Campo obrigatório.</span>");
                    		  }
		                }
        		}
              ],
              close: function(){
            		$("#fin_emissao").val('');
          			$("#fin_vencimento").val('');
              }
        });
        
    });
	$('body').on('click', 'a.config', function(e){
		e.preventDefault();
		$('div.pesquisa-grid').slideToggle();
	});
	table();
	
	// Local Storage
	if(localStorage.getItem('pesquisaFolhaPagamentoGrid') != null){
		var pesquisaStorage = JSON.parse(localStorage.getItem('pesquisaFolhaPagamentoGrid'));
		if(pesquisaStorage.id_funcionario != '' || pesquisaStorage.id_tp_pagamento != "" ){
			$('#id_funcionario').val(pesquisaStorage.id_funcionario);
			$('#id_tp_pagamento').val(pesquisaStorage.id_tp_pagamento);
			$('div.pesquisa-grid').slideToggle();
			table();
		}
	}
	
	$('body').on('change', '#id_funcionario, #id_tp_pagamento ', function(){
		table();
		montarLocalStoragepesquisa();
	});
	
	$('body').on('click', 'a.next-periodo, a.prev-periodo', function(){
		var idFuncionario   = $('#id_funcionario').val();
		var idTpPagamento 	= $('#id_tp_pagamento').val();
		var dt_periodo 		= $(this).attr('data-data');
		$.post('/rh/folha-de-pagamento/table',
				{id_funcionario:idFuncionario,dt_periodo:dt_periodo,id_tp_pagamento:idTpPagamento}, 
				function(data){
					$('div.portlet-body').html(data);
			})
		  .fail(function() {
			  	$('div.portlet-body').html('');
			    alert( "Ocorreu um erro no servidor." );
			  });
	})
});
function table(){
	$('div.portlet-body').html('');
	var idFuncionario   = $('#id_funcionario').val();
	var idTpPagamento 	= $('#id_tp_pagamento').val();
	$.post('/rh/folha-de-pagamento/table',
			{id_funcionario:idFuncionario,id_tp_pagamento:idTpPagamento}, 
			function(data){
				$('div.portlet-body').html(data);
		})
	  .fail(function() {
		  	$('div.portlet-body').html('');
		    alert( "Ocorreu um erro no servidor." );
		  });
}
function montarLocalStoragepesquisa(){
	var pesquisaStorage = {id_funcionario:$('#id_funcionario').val(), id_tp_pagamento:$('#id_tp_pagamento').val() };
	localStorage.setItem('pesquisaFolhaPagamentoGrid', JSON.stringify(pesquisaStorage));
}