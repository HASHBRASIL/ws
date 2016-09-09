$(document).ready(function(){
	
	$( ".datepicker" ).datepicker();
	$('.date').setMask();
	
	$("body").on('click', "a.novaJust", function(e){
		e.preventDefault();
		$("#data").blur();
		$('.diolog_form').dialog('open');
	});
	
	$("body").on('click', "a.ediJust", function(e){
		e.preventDefault();
		$("#data").blur();
		$("#id_rh_feriados").val($(this).attr('data-id'));
		$("#descricao").val($(this).attr('data-nome'));
		$("#data").val($(this).attr('data-data'));
		$('.diolog_form').dialog('open');
	});
	
    $('.diolog_form').dialog({
        modal: true,
        dialogClass: 'ui-dialog-darkorange',
        position: [($(window).width() / 2) - (450 / 2), 200],
        resizable: true,
        title: "Novo Feriado",
        width: 450,
        height: 350,
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
                		  if($("#data").val() == ""){
                				alert('Campo data é obrigatório.');
                				return false;
                			}
                		  if($("#descricao").val() == ""){
                				alert('Campo descrição é obrigatório.');
                				return false;
                			}
                		  $.ajax({
                				type : "POST",
                				url : "/rh/feriado/form",
                				data : $('.form_just').serialize(),
                				success : function(data){
                					alert("Feriado cadastrado!");
                					location.reload();
                				}
                			});
	                }
    		}
          ],
          close: function(){
        	  $("#descricao").val("");
        	  $("#id_rh_feriados").val("");
        	  $("#data").val("");
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
                    		  window.location.href="/rh/feriado/delete/id_rh_feriado/"+id;
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
        
    });
});