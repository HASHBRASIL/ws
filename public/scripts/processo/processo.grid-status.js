$(document).ready(function(){

	$('body').on('click','a.visualizar_processo',function(e){
	    e.preventDefault();
	    var href = $(this).attr('href');
	    $('#dialogProcesso').dialog({
            modal: true,
            title: "Produtos baixados",
            position: [($(window).width() / 2) - (600 / 2), 200],
            dialogClass: 'ui-dialog-caribbean-green',
            width: "550",
            height: "auto",
            open: function(){
                $.ajax({
                    type: "GET",
                    url: href,
                    success: function(data){
                        $('#dialogProcesso').html(data);
                        if($('#movimento-by-processo').height()+75 < 600){
                            $( "#dialogProcesso" ).dialog( "option", "height", 'auto' );
                        }else{
                            $( "#dialogProcesso" ).dialog( "option", "height", 600 );
                        }

                    }
                });
            },
            close: function(){
                $('#dialogProcesso').html('');
            }
        });
	});


   $('body').on('click', 'a.visualizar_financeiro', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('#dialogFinancial').dialog({
            modal: true,
            title: "Dados Financeiros",
            dialogClass: 'ui-dialog-caribbean-green',
            position: [($(window).width() / 2) - (1200 / 2), 200],
            width: "1200",
            height: "auto",
            open: function(){
                $.ajax({
                    type: "GET",
                    url: href,
                    success: function(data){
                        $('#dialogFinancial').html(data);
                        $('#financial_row').css('max-width', '100%');
                    }
                });
            },
            close: function(){
                $('#dialogFinancial').html('');
            }
        });
    });

	$(document).ready(function(){
	    $('.grid_datatable').dataTable({
	        "bJQueryUI": true,
	        "sPaginationType": "bootstrap",
	        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
	        "aLengthMenu": [
	                        [30, 50, 100, -1],
	                        [30, 50, 100, "All"] // change per page values here
	                    ],
	        "iDisplayLength": 50,
	        "aaSorting": [[ 0, "desc" ]],
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
	});

	$('body').on('click', '.title-datatable', function(){
	    var key = $(this).attr('key');console.log(key);
	    $("div#DataTables_Table_"+key+"_wrapper").slideToggle();
	});

	$('a.pesquisar').click(function(e){
	    e.preventDefault();
	    $("div.form_pesq_status").slideToggle();
	});



    $( "#sta_id_selected" ).autocomplete({
        source: "processo/status/autocomplete",
        minLength: 1,
        select: function( event, ui ) {
            $("#sta_id").val(ui.item.id);
        },
        search: function( event, ui ) {
            $("#sta_id").val("");
        }
     });

    $('img#addStatus').click(function(){
        if($.isNumeric( $("#sta_id").val())){
            var id = $('#sta_id').val();
            $('#grid_status').append('<input type="text" style="width: 90%;display:inline" value="'+$('#sta_id_selected').val()+'" disabled="disabled" class="remove_'+id+'" ><input type="hidden" class="remove_'+id+'" name="statusList[]"  value="'+id+'" >').
            append('<img class="removeStatus remove_'+id+'" id_status="'+id+'" removeinput="1" src="images/delete.png" style="cursor:pointer;padding-left: 10px;" title="excluir">');
            $('#sta_id_selected').val('');
            $("#sta_id").val('');
            $('#companySelected').html($('.removeStatus').length);
        }else{
            alert('Selecione um status.');
        }
    });

    $('body').on('click', '.removeStatus', function(){
        var id = $(this).attr('id_status');
        $('.remove_'+id).remove();
        $('#companySelected').html($('.removeStatus').length);
    });


    $('#tipoSacado').change(function() {

        sacado = $("#tipoSacado").val();

        if (sacado == 2){
            $("#grupo_sacado_div").hide('fast');
            $("#funcionario_sacado_div").hide('fast');
            $("#empresa_sacado_div").show('slow');

        }else if (sacado == 3){
            $("#empresa_sacado_div").hide('fast');
            $("#funcionario_sacado_div").hide('fast');
            $("#grupo_sacado_div").show('slow');

        }else if (sacado == 1){
            $("#empresa_sacado_div").hide('fast');
            $("#grupo_sacado_div").hide('fast');
            $("#funcionario_sacado_div").show('show');

        }

    });

    $( "#empresa_sacado" ).autocomplete({
        source: "empresa/empresa/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $("#empresas_id").val(ui.item.id);
        },
        search: function( event, ui ) {
        }
     });
});