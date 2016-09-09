$(document).ready(function(){
    $('#div-grid-arquivo').gridArquivo();
    $("#my-awesome-dropzone").dropzone({
        paramName: 'arquivo',
        dictDefaultMessage: "Arraste os arquivos para fazer o upload",
        url: 'processo/arquivo/form/pro_id/'+$('#pro_id').val(),
        clickable: true,
        enqueueForUpload: true,
        success: function(file, response){
            if(response.success){
                $('#div-grid-arquivo').gridArquivo();
                file.previewElement.classList.add("dz-success");
            }else{
                alert(response.mensagem[0].text);
                file.previewElement.classList.add("dz-error");
                file.previewElement.querySelector("[data-dz-errormessage]").textContent = response.mensagem[0].text;
            }
        }
    });

    $('body').on('click', 'a.remove-arquivo', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('#dialog_delete_arquivo').dialog({
            modal: true,
            dialogClass: 'ui-dialog-purple',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir imagem",
            width: 450,
            height: 150,
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Fechar",
                        click: function() {
                          $(this).dialog('close');
                        }
                      },
                      {
                         'class' : 'btn green',
                         "text" : "excluir",
                          click: function() {
                              $.ajax({
                                  type: "GET",
                                  url: href,
                                  success: function(data){
                                      if(data[0].type == 'success'){
                                          $.messageBox("Imagem excluido com sucesso.", 'success');
                                          $('#dialog_delete_arquivo').dialog('close');
                                          $('#div-grid-arquivo').gridArquivo();
                                      }else{
                                          $.messageBox("Não foi possivel excluir a imagem.", 'error');
                                      }
                                  },
                                  error: function(){
                                      alert('Ocorreu um erro inesperado entre em contato com o administrador.');
                                  }
                              });
                        }
                }
              ]
        });

    });
});


$.fn.gridArquivo = function(){
    var $this = $(this);
    var id_processo = $('#pro_id').val();
    if(!$.isNumeric( id_processo )){
        return false;
    }
    $.ajax({
        type: "GET",
        url: 'processo/arquivo/grid-processo/id_processo/'+id_processo,
        success: function(data){
            $this.html(data);
        },
        complete: function(){
        }
    });
}