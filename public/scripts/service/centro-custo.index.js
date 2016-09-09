$(document).ready(function(){
	$('body').on('click', 'a.deleteMoldal', function(e){
        e.preventDefault();
        var id = $(this).attr("value");
        var name = $(this).attr("name");
        $("#valueDelete").prepend(name);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-steelblue',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir Centro de Custo",
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
                    		  window.location.href="/service/centro-custo/delete/id/"+id;
		                }
        		}
              ],
              close: function(){
            	  $("#valueDelete").empty();
              }
        });
        
    });
});