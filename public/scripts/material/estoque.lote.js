$(document).ready(function(){
    $('a.transfer[quantidade="0.00"]').remove();
    $('a.transfer').click(function(e){
        e.preventDefault();
        var id_estoque      = $(this).attr('id_estoque');
        var id_workspace    = $(this).attr('id_workspace');
        var cod_lote        = $(this).attr('cod-lote');
        if($(this).attr('quantidade') == "0.00"){
            return;
        }
        
        $('#id_estoque').val(id_estoque);
        $('#workspace').val(id_workspace);
        $('#dialog-transfer').dialog({
                title:"Transferir workspace - "+cod_lote,
                dialogClass: 'ui-dialog-green',
                modal:true,
                position: [($(window).width() / 2) - (300 / 2), 200],
                width: 300,
                open: function(){
                },
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
                              "text" : "Salvar",
                               click: function() {
                                   var id_workspace = $('#id_workspace').val();
                                   if(id_workspace == ""){
                                       alert("Selecione o workspace que será transferido o estoque "+cod_lote);
                                       return;
                                   }
                                   if($('#workspace').val() == id_workspace){
                                       alert('Este produto se encontra neste workspace.');
                                       return;
                                   }

                                   $.ajax({
                                       type: "POST",
                                       url: '/material/movimento/transfer',
                                       data: {id_estoque: $('#id_estoque').val(), id_workspace: $('#id_workspace').val()},
                                       success: function(data){console.log(data.success);
                                           if(data.success == true){
                                               alert('salvo com sucesso');
                                               location.reload();
                                               
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
                    $('#id_estoque').val('');
                    $('#id_workspace').val('');
                    $('#workspace').val('');
                }
        });
    });
    
    $('a.extrato-lote').click(function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('#dialog-extrato').dialog({
            title:"Extrato de estoque",
            dialogClass: 'ui-dialog-green',
            modal:true,
            width: 700,
            position: [($(window).width() / 2) - (700 / 2), 200],
            maxHeight: 700,
            height: 'auto',
            open: function(){
                $.ajax({
                    type: "get",
                    url: href,
                    success: function(data){
                        $('#dialog-extrato').html(data);
                    }
                });
            },
            close: function(){
                $('#dialog-extrato').html('');
            }
        });
    });
});
