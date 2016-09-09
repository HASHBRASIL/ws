$(document).ready(function(){

	$( ".workspace" ).on( "click", function(e) {
        e.preventDefault();

		var id = $(this).attr("id");

		registerWorkspace(id, name);

	});

    // @todo ver onde tem isso
	//$( ".workspaceButtons" ).on( "click", function() {
    //
	//	var id = $(this).attr("id_workspace");
	//	registerWorkspace(id, name);
    //
	//});

    $( ".modulo" ).on( "click", function(e) {
        e.preventDefault();

        var id = $(this).attr("id");

        changeModule(id);
    });



});

function changeModule(id) {
    $.ajax({
        type: "POST",
        url: 'auth/grupo/change-module',
        data: { id: id },
        success: function(data){
            if(data.success == true) {
                window.location.reload();
            } else {
                alert("Não foi possivel selecionar o Modulo.");
            }
        }
    });

}


function registerWorkspace(id){

    $.ajax({
        type: "POST",
        url: 'auth/grupo/change',
        data: { id: id},
        success: function(data){
            if(data.success == true) {
                window.location.reload();
            } else {
                alert("Não foi possivel selecionar o Workspace.");
            }
        }
    });

}