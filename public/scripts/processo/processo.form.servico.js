$(document).ready(function(){
    $('#grid-servico').gridServico();

    //abre o dialog para cadastrar serviço no processo
    $("body").on("click", '.add-servico',function(event){
        $('div.dialog-servico').dialog({
            modal: true,
            title: "Cadastrar serviço",
            dialogClass: 'ui-dialog-caribbean-green',
            position: [($(window).width() / 2) - (500 / 2), 150],
            width: "500",
            height: "auto",
            open: function(event, ui) {
                $('.decimal').setMask('decimal');
                $('#quantidade_servico').setMask('9999999999');
                $( "#servico" ).autocomplete({
                     source: "service/service/autocomplete",
                     minLength: 2,
                     select: function( event, ui ) {
                         $("#id_servico_selected").val(ui.item.id);
                     },
                     search: function( event, ui ) {
                         $("#id_servico_selected").val('');
                     }
                  });

            },
            buttons: [
                      {
                          'text': 'Cancelar',
                          'class': 'btn red',
                          'click': function(){
                              $(this).dialog('close');
                          }
                      },
                      {
                          'text': 'Adicionar',
                          'class': 'btn green',
                          'click': function(){
                              if( $('#id_servico_selected').val() == "" ){
                                  alert('selecione um servico.');
                                  return;
                              }
                                  $.ajax({
                                      type: "POST",
                                      url: 'processo/processo-servico/form',
                                      data: {id_processo: $('#pro_id').val(), id_servico:$('#id_servico_selected').val(),
                                          quantidade: $('#quantidade_servico').val(), vl_unitario: $('#vl_unitario_servico').val(),
                                          total: $('#total_servico').val(), id: $('#id_processo_servico').val()},
                                      success: function(data){
                                          if( data.success){
                                              alert("Serviço cadastrado com sucesso.");
                                              $( 'div.dialog-servico' ).dialog( "close" );
                                              $('#grid-servico').gridServico();
                                          }else{
                                              alert(data.mensagem[0].text);
                                          }
                                      },
                                      beforeSend: function(){
                                          $('.ui-dialog-buttonset').hide();
                                      },
                                      complete: function(){
                                          $('.ui-dialog-buttonset').show();
                                      },
                                      error: function(){
                                          alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                                      }
                                  });
                              }
                      }
                      ],
              close: function(){
                  $("#id_servico_selected").val("");
                  $("#servico").val("");
                  $("#quantidade_servico").val("");
                  $("#vl_unitario_servico").val("");
                  $("#total_servico").val("");
              }
        });

    });//end add-servico

    //edita serviço
    $('body').on('click', '.editar_servico', function(e){
        e.preventDefault();
        var id_processo_servico = $(this).attr('id_processo_servico');
        $.ajax({
            type: "POST",
            url: 'processo/processo-servico/get',
            data: {id: id_processo_servico},
            success: function(data){
                $('#id_processo_servico').val(data.id_processo_servico);
                $('#servico').val(data.nome_servico);
                $('#id_servico_selected').val(data.id_servico);
                $('#quantidade_servico').val(data.quantidade);
                $('#vl_unitario_servico').val(data.vl_unitario ? data.vl_unitario.replace('.', ','):null);
                $('#total_servico').val(data.total? data.total.replace('.', ','): null);
                $('.add-servico').trigger('click');
            },
            beforeSend: function(){
                $('.ui-dialog-buttonset').hide();
            },
            complete: function(){
                $('.ui-dialog-buttonset').show();
            },
            error: function(){
                alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
        });
    });//end editar-serviço

    //excluir serviço
    $('body').on('click', '.excluir_servico', function(e){
        e.preventDefault();
        var id_processo_servico = $(this).attr('id_processo_servico');

        $('#delete-servico').dialog({
            modal: true,
            title: "Excluir serviço",
            dialogClass: 'ui-dialog-caribbean-green',
            position: [($(window).width() / 2) - (300 / 2), 150],
            width: "300",
            height: "auto",
            buttons: [
                      {
                          'text': 'Fechar',
                          'class': 'btn red',
                          'click': function(){
                              $('#delete-servico').dialog('close');
                          }
                      },
                      {
                          'text': 'Excluir',
                          'class': 'btn green',
                          'click': function(){
                              $.ajax({
                                  type: "POST",
                                  url: 'processo/processo-servico/delete',
                                  data: {id: id_processo_servico},
                                  success: function(data){
                                      if( data[0].type =="success"){
                                          alert('Serviço excluído com sucesso.');
                                          $('#grid-servico').gridServico();
                                          $('#delete-servico').dialog('close');
                                      }else{
                                          alert(data[0].text);
                                      }
                                  },
                                  beforeSend: function(){
                                      $('.ui-dialog-buttonset').hide();
                                  },
                                  complete: function(){
                                      $('.ui-dialog-buttonset').show();
                                  },
                                  error: function(){
                                      alert("Serviço temporariamente indisponivel. entre em contato com o administrador.");
                                  }
                              });
                          }
                      }
                      ]

        });
    });//excluir serviço


    //calcula o valor total do processo
    $('body').on('change', '#quantidade_servico, #vl_unitario_servico', function(){
        if($('#quantidade_servico').val() == ""){
            return;
        }
        var vl_unitario_servico = parseFloat($('#vl_unitario_servico').val().replace(/\./g, '').replace(',', '.'));
        var quantidade_servico  = parseFloat( $('#quantidade_servico').val() );
        $('#total_servico').val( decimal(vl_unitario_servico * quantidade_servico));
    });//endValor
});


$.fn.gridServico = function(){
    var id_processo= $('#pro_id').val();
    var isCompany = $('#isCompany').val();
    var $this = $(this);
    /* se quem estiver logado for uma empresa não irá mostrar a grid */
    if(isCompany == 1 || id_processo == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: "processo/processo-servico/grid/pro_id/"+id_processo,
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
            alert("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err1");
        }

    });
};