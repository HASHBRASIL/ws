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
        "aoColumnDefs": [
             {
                'bSortable': false,
                'aTargets': [0]
            }
        ],
    });
    $(".dataTables_wrapper").addClass('clearfix');
    
    $('body').on('change','.all-checkbox', function(e){
        if($(this).is(':checked')){
            $('input[type="checkbox"]').prop('checked', true);
            $('input[type="checkbox"]').uniform();
        }else{
            $('input[type="checkbox"]').prop('checked', false);
            $('input[type="checkbox"]').uniform();
        }
    });
    
    $('body').on('click','.produto-checkbox', function(e){
        if($('.produto-checkbox').filter(':not(:checked)').length == 0){
            $('.all-checkbox').prop('checked', true);
            $('input[type="checkbox"]').uniform();
        }else{
            $('.all-checkbox').prop('checked', false);
            $('input[type="checkbox"]').uniform();
        }
    });
    $('select[name="datatables_length"]').change(function(){
        if($('.produto-checkbox').filter(':not(:checked)').length == 0){
            $('.all-checkbox').prop('checked', true);
            $('input[type="checkbox"]').uniform();
        }else{
            $('.all-checkbox').prop('checked', false);
            $('input[type="checkbox"]').uniform();
        }
    });
    
    $('#mudar-grupo').click(function(){
        if( !$('.produto-checkbox').is(':checked') ){
            return;
        }
        
        $("#dialog-muda-grupo").dialog({
            modal: true,
            resizable: false,
            title: "Mudar grupo",
            dialogClass: 'ui-dialog-purple',
            heigth: 'auto',
            width: 600,
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
                               if($('#grupo').val() == ""){
                                   alert('Selecione o grupo.');
                                   return;
                               }else if($('#subgrupo').val() == "" && $('#subgrupo option').length > 1 ){
                                   alert('selecione o Subgrupo.');
                                   return;
                               }else if($('#classe').val() == "" && $('#classe option').length > 1){
                                   alert('Selecione a classe.');
                                   return;
                               }
                               $.ajax({
                                   type: "POST",
                                   url: "/material/item/mudar-grupo",
                                   data: $('.produto-checkbox').serialize()+
                                   "&id_grupo="+$('#grupo').val()+"&id_subgrupo="+$('#subgrupo').val()+
                                   '&id_classe='+$('#classe').val(),
                                   success: function(e){
                                       if(e.success){
                                           location.reload();
                                       }else{
                                           alert(e.mensagem[0].text);
                                       }
                                   },
                                   error: function(e){
                                       alert("Sistema está fora do ar entre em contato com o administrador.");
                                   }
                               });
                           }
                 }
               ],
            close: function(){
                $('#nome_unidade').val("");
            },
            open: function(){
                $('#subgrupo').parent().parent().hide();
                $('#classe').parent().parent().hide();
            }
        });
    });
    
    $('body').on('change', '#grupo', function(){
        $(this).selectSubGrupo();
    });
    $('body').on('change', '#subgrupo', function(){
        $(this).selectClasse();
    });
    
});

$.fn.selectSubGrupo = function(){
    var id_grupo = $(this).val();
    if(id_grupo == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: '/material/sub-grupo/pairs/id_grupo/'+id_grupo,
        success: function(data){
            var list = data.list;
            var count = 0;
            var html =  "<option value=''>---- Selecione ----</option>";
            $.each(list, function(key, value){
                html += "<option value='"+key+"'>"+value+"</option>";
                count++; 
            });
            if(count > 0){
                $('#subgrupo').parent().parent().show();
            }else{
                $('#subgrupo').parent().parent().hide();
            }
            $('#subgrupo').html(html);
            $('#subgrupo').selectClasse();
        },
        complete: function(){
        }
    });
};

$.fn.selectClasse = function(){
    var id_subgrupo = $(this).val();
    if(id_subgrupo == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: '/material/classe/pairs/id_subgrupo/'+id_subgrupo,
        success: function(data){
            var list = data.list;
            var count = 0;
            var html =  "<option value='' >---- Selecione ----</option>";
            $.each(list, function(key, value){
                html += "<option value='"+key+"'>"+value+"</option>";
                count++;
            });

            if(count > 0){
                $('#classe').parent().parent().show();
            }else{
                $('#classe').parent().parent().hide();
            }
            $('#classe').html(html);
        },
        complete: function(){
        }
    });
};