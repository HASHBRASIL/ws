$(document).ready(function(){
    $.extend( $.fn.dataTable.defaults, {
    } );
    // tradução do datatable
    $('#datatable').dataTable({
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[50, 100, 200], [50, 100, 200]],
            "iDisplayLength": 50,
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
             }
    });
    

    // tradução do datepicker
    $.datepicker.regional['pt-BR'] = {
            closeText: 'Fechar',
            prevText: '&#x3c;Anterior',
            nextText: 'Pr&oacute;ximo&#x3e;',
            currentText: 'Hoje',
            monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
            'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
            'Jul','Ago','Set','Out','Nov','Dez'],
            dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
            dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 0,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''};
    $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
    

    $.timepicker.regional['pt-BR'] = {
            timeOnlyTitle: 'Tempo',
            timeText: 'Hora',
            hourText: 'Hora',
            minuteText: 'Minuto',
            secondText: 'Segundo',
            currentText: 'Agora',
            closeText: 'Fechar'};
    $.timepicker.setDefaults($.timepicker.regional['pt-BR']);
    
    $('body').on( 'click', 'a.delete', function(e){
        var valid = confirm("Deseja realmente excluir este dado?");
        if(!valid){
            return false;
        }
    });
    
    setTimeout(function(){$('.large-12 > .alert-box').hide('slow');}, 5000);
    
    $(document).ajaxStart(function(){$("#carregando").show();});
    $(document).ajaxStop(function(){$("#carregando").hide();});
});

jQuery.messageBox = function(text, type){
    $('#message-box > div').addClass(type);
    $('span.text-message').text(text);
    $('#message-box').show('slow');
    setTimeout(function(){$('#message-box').hide('slow');$('#message-box > div').removeClass(type);}, 2000);
};