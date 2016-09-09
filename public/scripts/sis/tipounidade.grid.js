$(document).ready(function(){
	$('body').on('click', 'a.deleteMoldal', function(e){
        e.preventDefault();
        var id = $(this).attr("value");
        $("#valueDelete").prepend(name);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-grey',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir Unidade",
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
                    		  window.location.href="/sis/tipo-unidade/delete/id/"+id;
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
        
    });
    //abre o dialog de cadastro de endereço
    $('a.cadastro-link').click(function(e){
        e.preventDefault();
        $('.dialog_cadastro').dialog({
            modal: true,
            dialogClass: 'ui-dialog-blue',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Cadastra uma Unidade ",
            width: 450,
            height: 190,
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Cancelar",
                        click: function() {
                          $( this ).dialog( "close" );
                          $('#nome').val('');
                        }
                      },
                      {
                         'class' : 'btn blue',
                         "text" : "Enviar",
                          click: function() {
                            if($('.nome').val() == ""){
                                alert('O campo nome está vazio');
                                return;
                            }
                            $.ajax({
                                type: "POST",
                                url: '/sis/tipo-unidade/form',
                                data: $('.form-cadastro').serialize(),
                                success: function(data){
                                    if(data.success == true){
                                        $.messageBox("Unidade salvo com sucesso.", 'success');
                                        $('.dialog_cadastro').dialog('close');
                                        location.reload();
                                    }else{
                                        alert("Não foi possivel salvar a unidade.");
                                    }
                                }
                            });
                        }
                }
              ],
              close: function(){
                  $('#nome').val('');
              }
        });
        
    });
    
    $('a.editar-link').click(function(e){
        e.preventDefault();

        var id = $(this).attr("value");
        var nome = $(this).attr("titulo");
        $("#nome").val(nome);
        $('.dialog_cadastro').dialog({
        	modal: true,
            dialogClass: 'ui-dialog-blue',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Cadastra uma Unidade ",
            width: 450,
            height: 190,
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Cancelar",
                        click: function() {
                          $( this ).dialog( "close" );
                          $('#nome').val('');
                        }
                      },
                      {
                         'class' : 'btn blue',
                         "text" : "Enviar",
                          click: function() {
                            if($('.nome').val() == ""){
                                alert('O campo nome está vazio');
                                return;
                            }
                            $.ajax({
                                type: "POST",
                                url: '/sis/tipo-unidade/form/id_tipo_unidade/'+id,
                                data: $('.form-cadastro').serialize(),
                                success: function(data){
                                    if(data.success == true){
                                        $.messageBox("Unidade salvo com sucesso.", 'success');
                                        $('.dialog_cadastro').dialog('close');
                                        location.reload();
                                    }else{
                                        alert("Não foi possivel salvar a unidade.");
                                    }
                                }
                            });
                        }
                }
              ],
              close: function(){
                  $('#nome').val('');
              }
        });
        
    });
});