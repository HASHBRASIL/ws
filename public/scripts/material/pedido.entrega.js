$(document).ready(function(){
    // tradução do datatable
    $('#table_pedido').dataTable({
        "aoColumns": [
                      null,
                      null,
                      null,
                      null,
                      null,
                      null,
                      null,
                      null,
                      null,
                      null,
                      null,
                      { "bSortable": false }
                ],
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[50, 100, 200], [50, 100, 200]],
            "iDisplayLength": 50,
            "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
            "sPaginationType": "bootstrap",
            "oLanguage": {
                "sProcessing": "Aguarde enquanto os dados são carregados ...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "Não foram encontrados resultados",
                "sInfoEmpty": "Mostrando de 0 até 0 de 0 registros",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoFiltered": "",
                "sSearch": "Procurar",
                "oPaginate": {
                   "sFirst":    "Primeiro",
                   "sPrevious": "Anterior",
                   "sNext":     "Próximo",
                   "sLast":     "Último"
                }
             },
    });
    
    $('body').on('click', 'a.view_prod', function(e){
        e.preventDefault();
        var id = $(this).attr('href');
        $.ajax({
            type: "get",
            url: '/material/pedido/ver-produto/id_entrega/'+id,
            success: function(data){        
                $('div.view_prod').dialog({
                    modal: true,
                    resizable: true,
                    dialogClass: 'ui-dialog-purple',
                    position: [($(window).width() / 2) - (450 / 2), 200],
                    title: "Produto",
                    width: 450,
                    height: 'auto',
                    open: function(){
                        $('div.view_prod').html(data);
                    },
                  close: function(){
                  }
            });
            },
            error: function(data){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
        });
    });
    
    $('body').on('click', 'a.entrega_prod', function(e){
        e.preventDefault();
        var id_entrega = $(this).attr('href');
        var id_status  = $(this).attr('id-status');
        var protocolo  = $(this).attr('protocolo');
        $('div.dialog_entrega').dialog({
                modal: true,
                resizable: true,
                dialogClass: 'ui-dialog-purple',
                position: [($(window).width() / 2) - (450 / 2), 200],
                title: "Entrega",
                width: 450,
                height:'auto',
                open: function(){
                    $('#id').val(id_entrega);
                    $('#id_status').val(id_status);
                    $('#protocolo').val(protocolo);
                },
              close: function(){
                  $('#id').val("");
                  $('#id_status').val("");
                  $('#protocolo').val("");
              },
              buttons: 
              [
               {
                  'text': 'Cancelar',
                  'class': 'btn red',
                  "click": function() {
                      $('div.dialog_entrega').dialog( "close" );
                  },
               },
               {
                  'text': 'Adicionar',
                  'class': 'btn green',
                  "click": function() {
                      var protocolo = $('#id_protocolo').val();
                      if(protocolo == ""){
                          alert('preencha o campo protocolo de entrega.');
                          return;
                      }
                      $.ajax({
                          type: "POST",
                          url: '/material/entrega/form',
                          data: $('form.form_entrega').serialize(),
                          success: function(data){
                              if( data.success){
                                  $.messageBox("Entrega atualizado com sucesso.", 'success');
                                  location.href = "/material/pedido/entrega";
                              }else{
                                  console.log(data);
                              }
                              
                          },
                          error: function(){
                              alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                          }
                          
                      });
                      
                  }
               }
              ],
              
              
        });
    });
});