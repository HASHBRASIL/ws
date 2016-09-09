$(document).ready(function(){
    var id_item = $('#id_item').val();
    $("#my-awesome-dropzone").dropzone({
        paramName: 'arquivo',
        dictDefaultMessage: "Arraste os arquivos para fazer o upload",
        url: '/material/arquivo/form/id_item/'+id_item,
        clickable: true,
        enqueueForUpload: true,
        success: function(file, response){
            if(response.success){
                $('#grid_image').gridArquivo();
                file.previewElement.classList.add("dz-success");
            }else{
                alert(response.mensagem[0].text);
                file.previewElement.classList.add("dz-error");
                file.previewElement.querySelector("[data-dz-errormessage]").textContent = response.mensagem[0].text;
            }
        }
    });
    
    $('#grid_image').gridArquivo();

    //delete imagem
    $('body').on('click', '.delete_imagem', function(e){
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
                         "text" : "Cancelar",
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
                                          $('#grid_image').gridArquivo();
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
        
    });//endDeleteImagem
    
});
$.fn.gridArquivo = function(){
    var $this = $(this);
    var id_item = $('#id_item').val();
    if(!$.isNumeric( id_item )){
        return false;
    }
    $.ajax({
        type: "GET",
        url: '/material/arquivo/grid-item/id_item/'+id_item,
        success: function(data){
            $this.html(data);
        },
        complete: function(){
        }
    });
}