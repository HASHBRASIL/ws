$('document').ready(function(){
    $('.grid-financeiro').gridFinanceiro();

    //dialog do agrupador financeiro
    $('a.financial').click(function(e){
        e.preventDefault();
        $('.decimal').setMask('decimal');
        $('#agrupador-financeiro-box').dialog({
            modal: true,
            dialogClass: 'ui-dialog-caribbean-green',
            position: [($(window).width() / 2) - (550 / 2), 100],
            resizable: true,
            title: "Gerar agrupador financeiro",
            width: 550,
            height: 'auto',
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Fechar",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                      },
                      {
                          'class' : 'btn green',
                          "text" : "Cadastrar",
                         click: function() {
                             if($('#moe_id').val() == ""){
                                 alert("Selecione uma moeda");
                                 return false;
                             }

                             if($('#tmv_id').val() == ""){
                                 alert("Selecione o tipo de movimento finaneiro");
                                 return false;
                             }


                             $.ajax({
                                 type: "POST",
                                 url: "financial/agrupador-financeiro/form/",
                                 data: $('#agrupador-financeiro-form').serialize()+"&pro_id="+$("#pro_id").val()
                                 +"&fin_descricao="+$("#pro_desc_produto").val()
                                 +"&id_empresa="+$("#empresas_id").val()
                                 +"&id_workspace="+$('#id_workspace').val(),
                                 beforeSend: function(){
                                     $("#load").show();
                                 },
                                 success: function(data){
                                     if(data.success == true){
                                         alert("Registro Salvo com sucesso");
                                         $('.grid-financeiro').gridFinanceiro();
                                         $('#agrupador-financeiro-box').dialog('close');
                                     }else{
                                         alert(data.mensagem[0].text);
                                     }
                                 },
                                 complete: function(){
                                     $("#load").hide();
                                 },
                                 error: function(){
                                     alert("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err4");
                                 }

                             });
                         }
                       },
              ],
              close: function(){
                    $('#tmv_id').val('');
                    $('#fin_nota_fiscal').val('');
                    $('#moe_id').val('');
                    $('#fin_valor').val('');
                    $('#plc_id').val('');
                    $('#cec_id').val('');
                    $('#ope_id').val('');
                    $('#grupo_id').val('');
              }
        });
    });// end-dialog

    //mudança no select do tipo de movimento
    $('#tmv_id').change(function(){

        movimentoFinanceiro = $("#tmv_id").val();

        if (movimentoFinanceiro != ""){

            if(movimentoFinanceiro == 2){
                $('#fin_valor').val($('#pro_vlr_pedido').val());
            }else{
                $('#fin_valor').val('0,00');
            }

            $.ajax({
                type: "POST",
                url: 'financial/plano-contas/get-pairs-per-type',
                data: { type: movimentoFinanceiro},
                success: function(data){

                    if (data.success == "true"){

                        $("#plc_id").empty();

                        $.each( data.data, function( key, value ) {

                            $("#plc_id").prepend('<option value="'+key+'">'+value+'</option>');

                        });

                        $("#plc_id").prepend('<option selected = "selected" value="">----Selecione----</option>');

                    }else{
                        alert("Contacte o administrador. Os planos de contas não puderam localizados");
                    }
                }
            });
        }
    });//endmudanca

    //delete um agrupador financeiro
    $("body").on("click", 'a.delete_financial',function(e){
        e.preventDefault();
        var id = $(this).attr('value');

        $('#delete-agrupador-financeiro').dialog({
            modal: true,
            dialogClass: 'ui-dialog-caribbean-green',
            position: [($(window).width() / 2) - (350 / 2), 100],
            resizable: true,
            title: "Excluir agrupador financeiro",
            width: 350,
            height: 'auto',
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Fechar",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                      },
                      {
                          'class' : 'btn green',
                          "text" : "Excluir",
                         click: function() {
                             $.ajax({
                                 type: "POST",
                                 url: "financial/agrupador-financeiro/delete/id_agrupador_financeiro/"+id,
                                 data: { id: id},
                                 success: function(data){
                                      if(data[0].type == 'success'){
                                             $.messageBox("Dado excluido com sucesso.", 'success');
                                             $('.grid-financeiro').gridFinanceiro();
                                             $('#delete-agrupador-financeiro').dialog('close');
                                         }else{
                                             $.messageBox("Não foi possivel excluir o dado.", 'error');
                                         }
                                 }
                             });
                         }
                       },
              ],
              close: function(){
              }
        });
    });//endDeleteAgrupadorFinanceiro

});
$.fn.gridFinanceiro = function(){
    var id_processo = $('#pro_id').val();
    var $this       = $(this);
    if(id_processo == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: 'financial/agrupador-financeiro/grid-financial-processo-ajax/id_processo/'+id_processo,
        success: function(data){
            $this.html(data);
        },
        complete: function(){
        }
    });
};