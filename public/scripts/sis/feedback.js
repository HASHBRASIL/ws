function abreModal(){
    
    $('.dialog_feedback').dialog({
        modal: true,
        dialogClass: 'ui-dialog-blue',
        position: [($(window).width() / 2) - (450 / 2), 200],
        resizable: true,
        title: "Dê-nos sua opinião ",
        width: 450,
        height: 380,
        buttons: [
                  {
                     'class' : 'btn red',
                     "text" : "Cancelar",
                    click: function() {
                      $( this ).dialog( "close" );
                    }
                  },
                  {
                     'class' : 'btn blue',
                     "text" : "Enviar",
                      click: function() {
                        if($('.form-feedback select[name="id_tipo_feed"]').val() == 0){
                            alert('Selecione um assunto está vazio');
                            return;
                        }
                        if($('#feedback').val() == ""){
                            alert('O campo de mensagem está vazio');
                            return;
                        }
                        $.ajax({
                            type: "POST",
                            url: '/sis/feedback/form',
                            data: $('.form-feedback').serialize(),
                            success: function(data){
                                if(data.success == true){
                                    $.messageBox("Feedback enviado com sucesso.", 'success');
                                    $('.dialog_feedback').dialog('close');
                                }else{
                                    alert("Não foi possivel enviar o feedback.");
                                }
                            }
                        });
                    }
            }
          ],
          close: function(){
              $('#feedback').val('');
              $('.form-feedback select[name="id_tipo_feed"]').val(0);
              $('.form-feedback input[name="id_criacao_usuario"]').prop('checked', false);
              $('.form-feedback input[name="id_criacao_usuario"]').uniform();
          }
    });
    
};

$(document).ready(function(){
    
    $.getJSON( "/sis/feedback/assunto-feed", function( data ) {
        var option = new Array();
        
        $.each(data, function(key, value){
            option[key] = document.createElement('option');
            $( option[key] ).attr( {value : key} );
            $( option[key] ).append( value );
            $("select[name='id_tipo_feed']").append( option[key] );
        });

    });
    
    //abre o dialog de cadastro de endereço
    $('body').on('click','.feedback-link', function(e){
        e.preventDefault();
        abreModal();
    });

    
});

