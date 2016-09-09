$(document).ready(function(){
	
    $(".reciboProtocolo").on("click", function(event){
        var idProtocolo = $(this).attr("id_protocolo");
        window.open("/material/protocolo/recibo-entrega/id_protocolo/"+idProtocolo, '_blank');
    });

     $( "#dialog" ).dialog({
         autoOpen: false,
         modal: true,
         dialogClass: 'ui-dialog-purple',
         buttons: [
                   {
                       'class' : 'btn red',
                       "text" : "Cancelar",
                       click: function() {
                           $(this).dialog('close');
                       }
                   },
                   {
                       'class' : 'btn green',
                       "text" : "Gerar",
                       click: function() {
                           var saldo = 0;
                           var trel;
                           if($('#saldo').is(':checked')){
                               saldo = 1;
                           }
                           $('input.trel').each(function() {
                               //Verifica qual está selecionado
                               if ($(this).is(':checked'))
                                   trel = parseInt($(this).val());
                           });
                           window.open("/material/protocolo/relatorio/trel/"+trel+"/saldo/"+saldo+"/id_protocolo/"+$('#id_protocolo').val(), '_blank');
                           $(this).dialog('close');
                       }
                   }
                   ],
         close: function(){
             $('#id_protocolo').val('');
         }
     });
     $( "body" ).on('click', '.opener', function(e) {
         e.preventDefault();
         var id_protocolo = $(this).attr('id_protocolo');
         $('#id_protocolo').val(id_protocolo);
         $( "#dialog" ).dialog( "open" );
     });
	
});