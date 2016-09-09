$(document).ready(function(){

    $( "#empresa_sacado" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {

            var empresa_sacado = ui.item.label;
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

            $("#localEmpresa").prepend(html);
            $( "#empresa_sacado" ).val("");
            return false;
        },
        search: function( event, ui ) {
        },
     });


    $(document).on("click", ".remove-conta", function(){
        $(this).parent().parent().parent().parent().remove();
    });


    $('#de_fin_inclusao').datepicker({
        format: "dd/mm/yy",
        language: "pt-BR",
        onClose: function(selectedDate){
            $('#ate_fin_inclusao').datepicker( "option", "minDate", selectedDate );
        }
    });
    $('#ate_fin_inclusao').datepicker({
        language: "pt-BR",
        format: "dd/mm/yy",
        onClose: function(selectedDate){
            $('#de_fin_inclusao').datepicker( "option", "maxDate", selectedDate );
        }
    });


    $('#de_fin_competencia').datepicker({
        language: "pt-BR",
        format: "mm/yyyy",
        minViewMode: 1,
        autoclose: true,
        viewMode: 1,
        startDate: '-3y',
        onClose: function(selectedDate){
            $('#ate_fin_competencia').datepicker( "option", "minDate", selectedDate );
        }
    });
    $('#ate_fin_competencia').datepicker({
        language: "pt-BR",
        format: "mm/yyyy",
        autoclose: true,
        minViewMode: 1,
        viewMode: 1,
        startDate: '-3y',
        onClose: function(selectedDate){
            $('#de_fin_competencia').datepicker( "option", "maxDate", selectedDate );
        }
    });

    $('#de_fin_compensacao').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        onClose: function(selectedDate){
            $('#ate_fin_compensacao').datepicker( "option", "minDate", selectedDate );
        }
    });
    $('#ate_fin_compensacao').datepicker({
        language: "pt-BR",
        format: "dd/mm/yyyy",
        onClose: function(selectedDate){
            $('#de_fin_compensacao').datepicker( "option", "maxDate", selectedDate );
        }
    });

    $('#de_fin_vencimento').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        onClose: function(selectedDate){
            $('#ate_fin_vencimento').datepicker( "option", "minDate", selectedDate );
        }
    });
    $('#ate_fin_vencimento').datepicker({
        language: "pt-BR",
        format: "dd/mm/yyyy",
        onClose: function(selectedDate){
            $('#de_fin_vencimento').datepicker( "option", "maxDate", selectedDate );
        }
    });

    $(".datepicker").setMask({mask:'99/99/9999',autoTab: false});


    $( "#gerarPdf" ).click(function() {

        $( "#form" ).attr("target", "_blank");
        $( "#form" ).attr("action", "financial/contas/extrato-pdf");

    });

    $( "#gerarView" ).click(function() {

        $( "#form" ).attr("target", "");
        $( "#form" ).attr("action", "financial/contas/extrato-view");

    });

    $('body').on('click', '#todos-checkbox', function(){
        if($(this).is(':checked')){
            $('.grid-conta input[type="checkbox"]').prop('checked', true);
            $('.grid-conta input[type="checkbox"]').uniform();
        }else{
            $('.grid-conta input[type="checkbox"]').prop('checked', false);
            $('.grid-conta input[type="checkbox"]').uniform();
        }
    });

    $('body').on('click', '.grid-conta input[type="checkbox"]:not(#todos-checkbox)', function(){
        var allChecked = true;
        $('.grid-conta input[type="checkbox"]:not(#todos-checkbox)').each(function(index){
            if(!$(this).is(':checked')){
                allChecked = false;
                return;
            }
        });
        $('#todos-checkbox').prop('checked', allChecked);
        $('.grid-conta input[type="checkbox"]').uniform();
    });

});