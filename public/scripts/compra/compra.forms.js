$(document).ready(function(){	
	if($('#id_campanhas').val() != "" ){
		$(this).GridItensComprados();
		$(this).Compras();
		$('.links').css('display','none');
	}
	if($('.select-campanha li').length != 1){
		$('.vem').html('Escolha sua campanha!');
		$('body').delegate('#linkCampanha', 'click', function(evt){
			$('.vem').css('display','none');
			$('.form-actions').css('display','none');
			var id = $(this).attr("data-idCampanha");
			$.ajax({
				type: "POST",
				url: '/compra/compra/pesquisa-campanha',
				data: {
					id: id
				},
				success: function(data){
					$('.campoPesquisa').show();
					$('.campoPesquisa').html(data);
				}
			});
		});
	} else if($('.select-campanha li').length = 1) {
		var id = $('#linkCampanha').attr('data-idCampanha');
		$('.links').css('display','none');
		$('.vem').css('display','none');
		$('.form-actions').css('display','none');
		$.ajax({
			type: "POST",
			url: '/compra/compra/pesquisa-campanha',
			data: {
				id: id
			},
			success: function(data){
				$('.campoPesquisa').show();
				$('.campoPesquisa').html(data);
			}
		});
	}
	$('body').on('click', 'a#finalizar', function(evt){
		evt.preventDefault();
		$.ajax({
			type: 'POST',
			url: '/compra/compra/finalizar-compra',
			data: {
				id_compra : $(this).attr('value'),
				total : $(this).attr('total')
			},
			success: function(data){
				console.log(data);
				if(data.success == true){
          		  window.location.href="/compra/compra/grid";
				} else {
					$.messageBox(data.message[0].text, data.message[0].type);
					$('body').scrollTop('.alert');				} 
			}
		});
	});
	$('body').delegate('#btn-Pesquisa', 'click', function(evt){
		evt.preventDefault();
		var id = $("#id_campanhas").val();
		var id = $("#id_campanha").val();
		var nome = $("#nome").val();
		var tipo = $("#checked").val();
		if(nome == ''){
			$.messageBox("O campo de pesquisa está vazio.", 'error');
			$('body').scrollTop('.alert');		}else {			
			$.ajax({
				type: "POST",
				url: '/compra/compra/pesquisa-produto',
				data: {
					id: id, nome : nome, tipo : tipo
				},
				success: function(data){
					if(data){
						$('.form-actions').show();
						$('.campoAttr').show();
						$('.campoAttr').html(data);
					} else {
						$.messageBox("O produto não esta nesta campanha ou não existe.", 'error');
						$('body').scrollTop('.alert');					}
				}
			});
		}
	});

	$('body').delegate('.add', 'click', function(evt){
		evt.preventDefault();
		/**
		 * to do: Um dia refatorar este código retirando esta variável temporária.
		 * Vinicius Leonidas e Carlos Vinicius - 31/10/2013
		 */
		var stop = 0;
			if($("#quantidade").val() != '' && $("#quantidade").val() != 0){
			} else {
				$.messageBox("Quantidade não pode estar vazia ou zerada.", 'error');
				$('body').scrollTop('.alert');				return false;
			}
			$("select[name^='atributo']").each(function(){ 
				if($(this).val() == ''){
					$.messageBox("Selecione todos os atributos.", 'error');
					$('body').scrollTop('.alert');					stop = 1;
				}
			});
			
			if (stop == 1){
				return false;
			}
			
			if($('#id_compra').val() == ""){
				var id_campanha = $("#id_campanha").val();
				var id_consultor = $("#id_consultor").val();
				$.ajax({
					type : "POST",
					url : "/compra/compra/form",
					data : {
						id_campanha : id_campanha,
						id_consultor : id_consultor
					},
					success : function(data){
						$('#id_compra').val(data.id.id_compra);
						var compra = data.id.id_compra;
						$.ajax({
							type : "POST",
							url : "/compra/compra-item/form",
							data : $('.form-horizontal').serialize(),
							success : function(data){
								$('#nome').val("");
								$('.campoPesquisa').show();
								$('.links').addClass('disabled');
								$('.form-actions').css('display' , 'none');
								$('.campoAttr').css('display' , 'none');
								$(this).GridItensComprados();
								$.messageBox("Dado inserido com sucesso.", 'success');
								$('body').scrollTop('.alert');							}
						});
					}
				});
			}else{
				evt.preventDefault();
				$.ajax({
					type : "POST",
					url : "/compra/compra-item/form",
					data : $('.form-horizontal').serialize(),
					success : function(data){
						$('#nome').val("");
						$('.campoPesquisa').show();
						$('.links').addClass('disabled');
						$('.form-actions').css('display' , 'none');
						$('.campoAttr').css('display' , 'none');
						$(this).GridItensComprados();
						$.messageBox("Dado inserido com sucesso.", 'success');
						$('body').scrollTop('.alert');					}
				});
			}

	});
	$('body').on('click', 'a.deleteMoldal', function(e){
        e.preventDefault();
        var id = $(this).attr("value");
        $("#valueDelete").html(id);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-grey',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir Produto da Compra",
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
                    		  
                    		  $.ajax({
                    				type : "POST",
                    				url : "/compra/compra-item/delete",
                    				data : {
                    					id_compra_item : id
                    				},
                    				success : function(data){
                    					$(this).GridItensComprados();
                    				}
                    			});
                    		  $('#dialog_delete').dialog( "close" );
                    		  $.messageBox("Dado removido com sucesso.", 'success');
                    		  $('body').scrollTop('.alert');
		                }
        		}
              ],
              close: function(){
              }
        });
        
    });

	$('body').on('click', 'a.editMoldal', function(e){
        e.preventDefault();
		var id = $("#id_campanha").val();
        var compra = $(this).attr('value');
        var item = $(this).attr('item');
        $('.campoPesquisa').css('display' , 'none');
        $.ajax({
			type: "POST",
			url: '/compra/compra/pesquisa-produto',
			data: {
				id : id, id_item : item, id_compra_item : compra
			},
			success: function(data){
				$('.form-actions').show();
				$('.campoAttr').show();
				$('.campoAttr').html(data);
				$('.valorT').show();
		        $('#id_compra_item').val(compra);
			}
		});
    });
});
$.fn.GridItensComprados = function(){
	var compra = $('#id_compra').val();
	$.ajax({
		type : "POST",
		url : "/compra/compra/grid-comprando",
		data : {
			id_compra : compra
		},
		success : function(data){
			$('.grid-itens-comprados').html(data);
		}
	});
};
$.fn.Compras = function(){
	var id = $('#id_campanhas').val();
	$('.vem').css('display','none');
	$('.form-actions').css('display','none');
	$.ajax({
		type: "POST",
		url: '/compra/compra/pesquisa-campanha',
		data: {
			id: id
		},
		success: function(data){
			$('.campoPesquisa').show();
			$('.campoPesquisa').html(data);
		}
	});
};