$(document).ready(function(){
	
	$("body").on("click",".deleteMoldal", function(){
    	
        var id = $(this).attr("value");
        var name = $(this).attr("name");
        $("#valueDelete").prepend(name);
        
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-grey',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir Perfil",
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
                    		  window.location.href="/profile/profile/delete/id_au_profile/"+id;
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
        
    });
	
});