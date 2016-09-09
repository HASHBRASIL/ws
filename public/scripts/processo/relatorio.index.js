$(document).ready(function(){

    $( "#empresa_sacado" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#empresas_id").val(ui.item.id);

            var empresa_sacado = $("#empresa_sacado").val();
            var empresaId = ui.item.id;
            if(empresa_sacado == "" && empresaId == ""){
                return;
            }

            var html = '<div class="row-fluid">';
            html += '<div class="span12">';
            html += '<div class="control-group">';
            html += '<div class="controls">';
            html += '<input class="span11" type="text" id = "'+empresaId+'" value="'+empresa_sacado+'" disabled="disabled">';
            html += '<input type="hidden" name="empresaList[]" value="'+empresaId+'">';
            html += '<i class="icon-remove remove-empresa" title="Remover Empresa"></i>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            $('#contaSelected').show();
            $("#localEmpresa").prepend(html);
        },
        search: function( event, ui ) {
        },
        close: function( event, ui ) {$( "#empresa_sacado" ).val("");}
     });

    $('body').on('click', 'i.remove-empresa', function(e){
    	e.preventDefault();
    	$(this).parent().parent().parent().parent().remove();
    });

    $('#de_pro_data_inc').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        onClose: function(selectedDate){
            $('#para_pro_data_inc').datepicker( "option", "minDate", selectedDate );
        }
    });
    $('#para_pro_data_inc').datepicker({
        language: "pt-BR",
        format: "dd/mm/yyyy",
        onClose: function(selectedDate){
            $('#de_pro_data_inc').datepicker( "option", "maxDate", selectedDate );
        }
    });


    $(".datepicker").setMask({mask:'99/99/9999',autoTab: false});


    $( "#gerarPdf" ).click(function() {

        $( "#form" ).attr("target", "_blank");
        $( "#form" ).attr("action", "processo/relatorio/pdf");

    });

    $( "#gerarView" ).click(function() {
        $( "#form" ).attr("action", "processo/relatorio/grid");
    });

    $('#gerarAnalitico').click(function(){
    	 $( "#form" ).attr("target", "_blank");
         $( "#form" ).attr("action", "processo/relatorio/pdf-analitico");
    });

    $('body').on('click', '#todos-checkbox', function(){
        if($(this).is(':checked')){
            $('input[type="checkbox"]').prop('checked', true);
            $('input[type="checkbox"]').uniform();
        }else{
            $('input[type="checkbox"]').prop('checked', false);
            $('input[type="checkbox"]').uniform();
        }
    });

});