$(document).ready(function(){
    $('#datatables').dataTable({
        "aLengthMenu": [
            [30, 50, 100, -1],
            [30, 50, 100, "All"] // change per page values here
        ],
        // set the initial value
        "iDisplayLength": 30,
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
        "aoColumnDefs": [{
                'bSortable': true,
                'aTargets': [0]
            }
        ],
    });
    $(".dataTables_wrapper").addClass('clearfix');
    
    
});