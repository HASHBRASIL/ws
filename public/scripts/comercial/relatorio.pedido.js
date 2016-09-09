$(document).ready(function(){
    
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
    
    $('#gerarAnalitico').click(function(){
    	 $( "#form" ).attr("target", "_blank");
         $( "#form" ).attr("action", "/processo/relatorio/pdf-analitico");
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
    console.log($('#id_workspace').val() == null);
    if($('#id_workspace').val() == null){
        $('#id_workspace').select2({
            placeholder: "Selecione um workspace"
        });
    }
    
    $('#empresa_sacado').select2({
           placeholder: 'Selecione entidade',
           multiple: true,
           ajax: {
               url: '/empresa/empresa/autocomplete',
               dataType: 'json',
               data: function (term, page) {
                   return { term: term };
               },
               results: function (data, page) {
                   return { results: data };
               }
           },
           formatResult: function (item) {
               return item.value;
           },
           formatSelection: function (item) {
               return item.value;
           }
       });
    
});