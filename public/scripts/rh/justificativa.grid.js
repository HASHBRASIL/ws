$(document).ready(function(){
	
	$("body").on('click', "a.novaJust", function(e){
		e.preventDefault();
		$('.diolog_form').dialog('open');
	});
	
	$("body").on('click', "a.ediJust", function(e){
		e.preventDefault();
		$("#id_rh_justificacao_ponto").val($(this).attr('data-id'));
		$("#descricao").val($(this).attr('data-nome'));
		$('.diolog_form').dialog('open');
	});
	
    $('.diolog_form').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        position: [($(window).width() / 2) - (450 / 2), 200],
        resizable: true,
        title: "Nova Justificativa",
        width: 450,
        height: 200,
        autoOpen: false,
        draggable: false,
        open: function() {
            $(this).css('overflow', 'hidden');
        },
        buttons: [
                  {
	                 'class' : 'btn',
	                 "text" : "Cancelar",
	                click: function() {
	                  $( this ).dialog( "close" );
	                }
                  },
                  {
	                 'class' : 'btn green',
	                 "text" : "Salvar",
                	  click: function() {
                		  $.ajax({
                				type : "POST",
                				url : "/rh/justificativa/form",
                				data : $('.form_just').serialize(),
                				success : function(data){
                					alert("Justificativa cadastrada!");
                					location.reload();
                				}
                			});
	                }
    		}
          ],
          close: function(){
        	  $("#descricao").val("");
        	  $("id_rh_justificacao_ponto").val("");
          }
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
                    		  window.location.href="/rh/justificativa/delete/id_rh_justificacao_ponto/"+id;
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
        
    });
});