$(document).ready(function(){

    $( ".modulo" ).on( "click", function() {

        var id = $(this).attr("id");

        changeModule(id);
    });


});

function changeModule(id) {
    alert('aqui????');

    $.ajax({
        type: "POST",
        url: '/auth/grupo/change-module',
        data: { id: id},
        success: function(data){
            if(data.success == true) {
                window.location.reload();
            } else {
                alert("Não foi possivel selecionar o Modulo.");
            }
        }
    });

}