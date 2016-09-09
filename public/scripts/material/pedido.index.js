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
    $(".dataTables_wrapper").addClass('clearfix');
    
    $('body').on('click', 'a.view_prod', function(e){
        e.preventDefault();
        var id = $(this).attr('href');
        $.ajax({
            type: "get",
            url: '/material/pedido/ver-produto/id_entrega/'+id,
            success: function(data){        
                $('div.view_prod').dialog({
                    modal: true,
                    dialogClass: 'ui-dialog-purple',
                    resizable: true,
                    position: [($(window).width() / 2) - (450 / 2), 200],
                    title: "Adicionar Produto",
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

    $('body').on('click', 'a.protocolo', function(e){
        e.preventDefault();
        var protocolo = $(this).attr('protocolo');
        $('div.dialog_protocolo').dialog({
            modal: true,
            resizable: true,
            dialogClass: 'ui-dialog-purple',
            position: [($(window).width() / 2) - (450 / 2), 200],
            title: "Protocolo de entrega",
            width: 450,
            height: 'auto',
            open: function(){
                if(protocolo == ""){
                    protocolo = "Não possui protocolo de entrega";
                }
                $('p.protocolo').html(protocolo);
            },
          close: function(){
          }
        });
    });
});