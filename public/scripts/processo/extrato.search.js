$(document).ready(function(){
    $( "#entidade" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#id_empresa_selecionado").val(ui.item.id);
        },
        search: function( event, ui ) {
            $('#id_empresa_selecionado').val('');
        }
     });
    $('i.add-entidade').click(function(e){
        e.preventDefault();
        var id_empresa = $('#id_empresa_selecionado').val();
        if(id_empresa == ""){
            return;
        }

        var html = '<input type="text" value="'+$('#entidade').val()+'" class="span8 empresa-'+id_empresa+'" disabled="disabled"> '+
                   '<i class="delete-entidade icon-remove" id-empresa="'+id_empresa+'"></i>'+
                   '<input type="hidden" name="id_empresa[]" value="'+id_empresa+'" class="empresa-'+id_empresa+'">';
        $('div.empresa-selecionada').append(html);
        $('#entidade').val('');
        $('#entidade').focus();
    });
    $('body').on('click', 'i.delete-entidade', function(e){
        e.preventDefault();
        var id_empresa = $(this).attr('id-empresa');
        $('.empresa-'+id_empresa).remove();
        $(this).remove();

    });

    $( "#dt_inicio" ).datepicker({
      changeMonth: true,
      onClose: function( selectedDate ) {
        $( "#dt_fim" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#dt_fim" ).datepicker({
        changeMonth: true,
        onClose: function( selectedDate ) {
          $( "#dt_inicio" ).datepicker( "option", "maxDate", selectedDate );
        }
      });

    $('body').on('click', '#checkbox-todo',function(){
        if($(this).is(':checked')){
            $('input[type="checkbox"]').prop('checked', true);
            $('input[type="checkbox"]').uniform();
        }else{
            $('input[type="checkbox"]').prop('checked', false);
            $('input[type="checkbox"]').uniform();
        }
    });
});