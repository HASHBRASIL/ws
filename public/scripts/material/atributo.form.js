$(document).ready(function(){
	$('#grid_opcao').gridOpcao();
    //abre o dialog de cadastro de endereço
    $('a.opcao').click(function(e){
        e.preventDefault();
        $('.dialog_opcao').dialog({
            modal: true,
            dialogClass: 'ui-dialog-purple',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Cadastrar opção",
            width: 450,
            height: 200,
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Cancelar",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                      },
                      {
                         'class' : 'btn green',
                         "text" : "Adicionar",
                          click: function() {
                            if($('#nome_opcao').val() == ""){
                                alert('O campo nome da opção está vazio');
                                return;
                            }
                            
                            $.ajax({
                                type: "POST",
                                url: '/material/opcao/form',
                                data: {nome: $('#nome_opcao').val(), id_atributo: $('#id_atributo').val(), id_opcao:$('#id_opcao').val()},
                                success: function(data){console.log(data.success);
                                    if(data.success == true){
                                        if($('#id_atributo').val() =="" ){
                                            $.messageBox("Opção inserido com sucesso.", 'success');
                                            
                                        }else if(data.success == false){
                                            $.messageBox(data.mensagem[0].text, 'error');
                                        }else{
                                            $.messageBox("Opção atualizado com sucesso.", 'success');
                                        }
                                        $('.dialog_opcao').dialog('close');
                                        $('#grid_opcao').gridOpcao();
                                    }else if(data.success == false){
                                        alert(data.mensagem[0].text);
                                    }else{
                                        alert("Não foi possivel salvar a opção.");
                                    }
                                }
                            });
                        }
                }
              ],
              close: function(){
                  $('#nome_opcao').val('');
                  $('#id_opcao').val('');
              }
        });
        
    });
    $('body').on('click', '.editar_opcao', function(e){
        e.preventDefault();
        var id_opcao = $(this).attr('id_opcao');
        $.ajax({
            type: "POST",
            url: '/material/opcao/get',
            data: {id_opcao:id_opcao},
            success: function(data){
                $('#id_opcao').val(data.id_opcao);
                $('#nome_opcao').val(data.nome);
                $('a.opcao').trigger('click');
            }
        });
    });
    
    // irá inativar uma opção
    $('body').on('click', 'a.delete_opcao', function(e){
        e.preventDefault();
        var id_opcao = $(this).attr('id_opcao');
        $('#valueDelete').html(id_opcao);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-red',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir opção",
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
                                  url: '/material/opcao/delete/id_opcao/'+id_opcao,
                                  success: function(data){
                                      if(data[0].type == 'success'){
                                          $.messageBox("Dado excluido com sucesso.", 'success');
                                          $('#dialog_delete').dialog('close');
                                          $('#grid_opcao').gridOpcao();
                                      }else{
                                          $.messageBox("Não foi possivel excluir o dado.", 'error');
                                      }
                                  },
                                  error: function(){
                                      alert('Ocorreu um erro inesperado entre em contato com o administrador.');
                                  }
                              });
                        }
                }
              ],
            close: function(){
                $('#valueDelete').html('');
            }
        });
        
    });
});

$.fn.gridOpcao = function(){
	var id_atributo = $('#id_atributo').val();
	var $this = $(this);
    $.ajax({
        type: "GET",
        url: '/material/atributo/grid-opcao/id_atributo/'+id_atributo,
        success: function(data){
            $this.html(data);
        }
    });
};

