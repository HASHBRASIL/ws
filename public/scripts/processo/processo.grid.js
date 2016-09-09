$(document).ready(function(){

	$(".pro_data_entrega").css(
			{
				"width":"10%"
			}
		);
	$(".pro_prazo_entrega").css(
			{
				"width":"20%"
			}
		);
	$(".cliente").css(
			{
				"width":"15%"
			}
		);
	$(".pro_quantidade").css(
			{
				"width":"10%"
			}
		);

	$('a.visualizar_processo').click(function(e){
	    e.preventDefault();
	    var href = $(this).attr('href');
	    $('#dialogProcesso').dialog({
            modal: true,
            dialogClass: 'ui-dialog-caribbean-green',
            title: "Produtos baixados",
            position: [($(window).width() / 2) - (600 / 2), 200],
            width: "650",
            height: "auto",
            open: function(){
                $.ajax({
                    type: "GET",
                    url: href,
                    success: function(data){
                        $('div#dialogProcesso').html(data);
                        if($('#movimento-by-processo').height()+75 < 600){
                            $( "#dialogProcesso" ).dialog( "option", "height", "auto" );
                        }else{
                            $( "#dialogProcesso" ).dialog( "option", "height", 600 );
                        }

                    }
                });
            },
            close: function(){
                $('div#dialogProcesso').html('');
            }
        });
	});

   $('a.visualizar_financeiro').click(function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('#dialogFinancial').dialog({
            modal: true,
            dialogClass: 'ui-dialog-caribbean-green',
            title: "Dados Financeiros",
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