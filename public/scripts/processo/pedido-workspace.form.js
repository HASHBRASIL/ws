    com_github_culmat_jsTreeTable.register(this);
    treeTable($('#table-tree'));
$(document).ready(function(){
    $('#pro_vlr_unt, #pro_vlr_pedido').setMask('decimal');
    //codigo de processo
    $('#altera-codigo').blur(function(){
        var cod = $(this).attr('data-pro-codigo');
        var value = $(this).val();
        if(value != cod){
            $(this).val(cod);
        }
    });

    //autocomplete da entidade de entrega
    $( '#altera-codigo' ).autocomplete({
        source: "processo/processo/autocomplete-codigo",
        select: function( event, ui ) {
            window.location.href="processo/pedido-workspace/form/id_processo/"+ui.item.id;
        }
      });//endautocomplete

    //

  //define a short hand

$('tr[data-tt-level="1"] td span').css("padding-left", '10px');

});