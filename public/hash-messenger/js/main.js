$(document).ready(function(){
    // Abas
    $('#tabContent a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })

    // Player
    $( function() {
        $('#slider').slider();
    });

    // Datepicker
    $('.calendar').click(function() {
      $('.datepicker').datepicker('show');
    });

    $('.calendar-date-end').click(function() {
      $('.datepicker-end').datepicker('show');
    });

    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        toggleActive: true
    });

    $('.datepicker-end').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR',
        toggleActive: true
    });

    // Clockpicker
    $('.clock-full').click(function(e) {
        e.preventDefault();
        $('.clockpicker').clockpicker('show');
    });

    $('.clockpicker').clockpicker({
        donetext: 'Salvar',
        placement: 'bottom',
        align: 'left'
    });
});
