$(document).ready(function(){
	$('body').on('click', 'a.importaMoldal', function(e){
        e.preventDefault();
        var id = $('#id_ponto').val();
        var arquivo = $.trim($('#nome-arquivo').text());
        if(arquivo == ""){
            alert('Não possui nenhum arquivo.');
            return;
        }
        $("#valueArquivo").text(arquivo);
        $('#dialog_importa').dialog({
            modal: true,
            dialogClass: 'ui-dialog-darkorange',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Importa Registro do Ponto",
            width: 450,
            height: 200,
            buttons: [
                      {
		                 'class' : 'btn green',
		                 "text" : "Cancelar",
		                click: function() {
		                  $( this ).dialog( "close" );
		                }
                      },
                      {
		                 'class' : 'btn red',
		                 "text" : "Importar",
                    	  click: function() {
                    		  $.ajax({
                                  type: "POST",
                                  url: '/rh/ponto/importa-pontos',
                                  data: { id_ponto : id},
                                  beforeSend: function(){
                                      ajaxLoading();
                                  },
                                  success: function(data){
                                      if(data.success){
                                    	  $('#dialog_importa').dialog('close');
                                          alert(data.total+' registros importado para o banco de dados.');
                                      }else{
                                    	  $('#dialog_importa').dialog('close');
                                    	  alert("Error! Tente novamente.");
                                      }
                                  },
                                  error: function(){
                                      $('#dialog_importa').dialog('close');
                                      alert("Error! Tente novamente.");
                                  }
                              });
		                }
        		}
              ],
              close: function(){
                  $('.spinner').css('display', 'none');
              }
        });
    });
});

function ajaxLoading()
{
    var z_index_dialog = $('.ui-dialog').css('z-index');
    $('div.ui-widget-overlay.ui-front').css('z-index',z_index_dialog+1);
    $('.spinner').show().css('z-index',z_index_dialog+2);
}
