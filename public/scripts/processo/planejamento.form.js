$(document).ready(function(){
    $('#data_inline').datepicker({
          changeMonth: true,
          changeYear: true,
          altField: "#date_select",
          altFormat: "d-mm-yy",
          onSelect: function(){
              $('#grid-planejamento').gridPlanejamento();
          }
      });
    $( "#data_inline" ).datepicker( "option", $.datepicker.regional[ 'pt-BR' ] );
    $(this).viewProcesso();
    $('#grid-planejamento').gridPlanejamento();
    //autocomplete da entidade
    $( "#cod_processo" ).autocomplete({
        source: "processo/processo/autocomplete",
        minLength: 2,
        select: function( event, ui ) {
            $('#id_processo').val(ui.item.id);
            $(this).viewProcesso();
        },
        search: function( event, ui ) {
            $('#id_processo').val("");
        }
      });//endautocomplete

    $('.salvar_planejamento').click(function(e){
        e.preventDefault();
        id_processo = $('#id_processo').val();
        if(id_processo == ""){
            alertModal("Selecione um processo.");
            return;
        }

        $.ajax({
            type: "POST",
            url: "processo/planejamento/form",
            data: {'id_processo': $('#id_processo').val(), 'id_prioridade': $('#id_prioridade').val(), 'ordem':$('#ordem').val(), 'data': $('#date_select').val(), 'id_planejamento': $('#id_planejamento').val()},
            success: function(data){
                if(data.success == false){
                    alertModal('Não foi possivel salvar.');
                    return;
                }
                $('#cod_processo').val('');
                $('#id_processo').val('');
                $('#id_prioridade').val(1);
                $('#ordem').val('');
                $('#grid-planejamento').gridPlanejamento();
                $(this).viewProcesso();
                alertModal("Planejamento salvo com sucesso.");
            },
            complete: function(){
                $("#load").hide();
            },
            error: function(){
                alertModal("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err1");
            }

        });


    });

    $('body').on('click','a.cancelar_planejamento', function(e){
        e.preventDefault();
        var id_planejamento = $(this).attr('id_planejamento');
        $('#dialog_delete_arquivo').dialog({
            modal: true,
            dialogClass: 'ui-dialog-caribbean-green',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir planejamento",
            width: 450,
            height: 150,
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Fechar",
                        click: function() {
                          $(this).dialog('close');
                        }
                      },
                      {
                         'class' : 'btn green',
                         "text" : "excluir",
                          click: function() {
                              $.ajax({
                                  type: "GET",
                                  url: 'processo/planejamento/delete/id_planejamento/'+id_planejamento,
                                  success: function(data){
                                      if(data[0].type == 'success'){
                                          $.messageBox("Planejamento excluido com sucesso.", 'success');
                                          $('#dialog_delete_arquivo').dialog('close');
                                          $('#grid-planejamento').gridPlanejamento();
                                      }else{
                                          alertModal("Não foi possivel excluir o planejamento.");
                                      }
                                  },
                                  error: function(){
                                      alertModal('Ocorreu um erro inesperado entre em contato com o administrador.');
                                  }
                              });
                        }
                }
              ]
        });

    });

    $('body').on('click', 'a.editar_planejamento', function(e){
        e.preventDefault();
        var id_planejamento = $(this).attr('id_planejamento');
        $.ajax({
            type: "GET",
            url: 'processo/planejamento/get/id_planejamento/'+id_planejamento,
            success: function(data){
                $('#cod_processo').val(data.cod_processo);
                $('#id_prioridade').val(data.id_prioridade);
                $('#id_processo').val(data.id_processo);
                $('#ordem').val(data.ordem);
                $('#id_planejamento').val(data.id_planejamento);
                $(this).viewProcesso();
            },
            error: function(){
                alertModal('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });
});


$.fn.gridPlanejamento = function(){
    var date = $('#date_select').val();
    var $this = $(this);
    /* se quem estiver logado for uma empresa não irá mostrar a grid */
    if(date == ''){
        return;
    }
    $.ajax({
        type: "GET",
        url: "processo/planejamento/grid-by-date/data/"+date,
        beforeSend: function(){
            $("#load").show();
        },
        success: function(data){
            $this.html(data);
        },
        complete: function(){
            $("#load").hide();
        },
        error: function(){
            alertModal("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err1");
        }

    });
};

$.fn.viewProcesso = function(){
    var id_processo = $('#id_processo').val();
    /* se quem estiver logado for uma empresa não irá mostrar a grid */
    if(id_processo == ''){
        $('div.view-processo').hide('slow');
        $('.text.cod_processo').text('');
        $('.text.entidade').text('');
        $('.text.quantidade').text('');
        $('.text.descricao').text('');
        return;
    }
    $('div.view-processo').show('slow');
    $.ajax({
        type: "GET",
        url: "processo/processo/get/id_processo/"+id_processo,
        beforeSend: function(){
            $("#load").show();
        },
        success: function(data){
            $('.text.cod_processo').text(data.pro_codigo);
            $('.text.entidade').text(data.entidade);
            $('.text.quantidade').text(data.pro_quantidade);
            $('.text.descricao').text(data.pro_desc_produto);
        },
        complete: function(){
            $("#load").hide();
        },
        error: function(){
            alertModal("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err1");
        }

    });
};

function alertModal(text){
    $('#alert-modal div.modal-body p').text(text);
    $('#alert-modal').modal('show');
}