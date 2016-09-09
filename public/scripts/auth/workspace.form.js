$(document).ready(function(){
    $( "#empresa" ).autocomplete({
        source: "/empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_empresa').val(ui.item.id);
        },
        search: function( event, ui ) {
            $('#id_empresa').val("");
        }
      });
});