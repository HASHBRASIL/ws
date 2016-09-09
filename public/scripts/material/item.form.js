$(document).ready(function(){
    $('.decimal').setMask('decimal');
    $('#grid_atributo').gridOpcao();
    $('textarea').limit('255','#countDescricao');
    $( "#qtd_compra, #qtd_consumo" ).spinner({
        min: 0
    });
    
    $('#combo_grupo').selectSubGrupo();

    $('body').on('change', '#combo_grupo', function(){
        $(this).selectSubGrupo();
        $('#combo_subgrupo').selectClasse();
    });
    
    $('#combo_subgrupo').selectClasse();

    $('body').on('change', '#combo_subgrupo', function(){
        $(this).selectClasse();
    });
    $("#ncm_sh").setMask({mask:'99999999', autoTab: false});
    $('.add-unidade').click(function(){
        $("#dialog-unidade").dialog({
            modal: true,
            resizable: false,
            title: "Tipo de unidade",
            dialogClass: 'ui-dialog-purple',
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
                               var nome_unidade = $('#nome_unidade').val();
                               if(nome_unidade == ""){
                                   alert('O Campo nome de unidade está vazio.');
                                   return;
                               }
                               $.ajax({
                                   type: "POST",
                                   url: "/sis/tipo-unidade/form",
                                   data: {'nome': $('#nome_unidade').val() },
                                   success: function(e){
                                       if(e.success){
                                           $.messageBox("Unidade cadastrada com sucesso.", 'success');
                                           $('#id_tipo_unidade_compra, #id_tipo_unidade_consumo').append("<option value='"+e.tipo_unidade.id+"'>"+e.tipo_unidade.nome+"</option>");
                                           $('#dialog-unidade').dialog('close');
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
            }
        });
    });
    
    //multiselect das opções
    $('#mult_opcao').multiSelect({
        selectableHeader: '<a href="#" class="btn grupo_selectAll" style="width:134px"><i class="icon-plus"></i> Marcar todos</a>',
        selectionHeader:  '<a href="#" class="btn grupo_deselectAll" style="width:134px"><i class="icon-minus"></i> Desmarcar todos</a>',
        afterSelect: function(values){
            $('#mult_opcao [value="'+values+'"]').attr('selected', 'selected');
        },
        afterDeselect: function(values){
            $('#mult_opcao [value="'+values+'"]').removeAttr('selected');
        }
      });
    
    $('body').on('click', '.grupo_selectAll', function(e){
        e.preventDefault();
        $('#mult_opcao').multiSelect('select_all');
        $('#mult_opcao option').attr('selected', 'selected');
    });
    $('body').on('click', '.grupo_deselectAll', function(e){
        e.preventDefault();
        $('#mult_opcao').multiSelect('deselect_all');
        $('#mult_opcao option').removeAttr('selected');
    });
    //endmultiselect

    //abre o dialog de atributos
    $('a.atributo').click(function(e){
        e.preventDefault();
        $('.dialog_atributo').dialog({
            modal: true,
            dialogClass: 'ui-dialog-purple',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Cadastrar atributo",
            width: 450,
            height: 480,
            buttons: [
                      {
                         'class' : 'btn red',
                         "text" : "Cancelar",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                      },
                      {
                         'class' : 'btn green',
                         "text" : "Adicionar",
                          click: function() {
                            $.ajax({
                                type: "POST",
                                url: '/material/item-opcao/form',
                                data: $('#formAtributo').serialize()+'&id_item='+$('#id_item').val(),
                                success: function(data){
                                    if(data.success == true){
                                        $.messageBox("Opção atualizado com sucesso.", 'success');
                                        $('.dialog_atributo').dialog('close');
                                        $('#grid_atributo').gridOpcao();
                                    }else{
                                        alert(data.mensagem[0].text);
                                    }
                                }
                            });
                        }
                }
              ],
              close: function(){
                  $('#id_atributo').val('');
                  html = '<select multiple="multiple" id="mult_opcao" name="opcao[]" style="margin-left: 0">';
                  html += '</select>';
                  $('#ms-mult_opcao, #mult_opcao').remove();
                  $('div.htmlOpcao').html(html);
                  $('#mult_opcao').multiSelect({
                      selectableHeader: '<a href="#" class="btn grupo_selectAll" style="width:134px"><i class="icon-plus"></i> Marcar todos</a>',
                      selectionHeader:  '<a href="#" class="btn grupo_deselectAll" style="width:134px"><i class="icon-minus"></i> Desmarcar todos</a>',
                      afterSelect: function(values){
                          $('#mult_opcao [value="'+values+'"]').attr('selected', 'selected');
                      },
                      afterDeselect: function(values){
                          $('#mult_opcao [value="'+values+'"]').removeAttr('selected');
                      }
                    });
              }
        });
        
    });
    // endDialogAtributo
    
    //mudança do select de atributo modificando todas as opções
    $('#id_atributo').change(function(){
        var id_atributo = $(this).val();
        if(id_atributo == ""){
            return;
        }
        $.ajax({
            type: "GET",
            url: '/material/opcao/get-pairs-by-atributo/id_atributo/'+id_atributo,
            success: function(data){
                html = '<select multiple="multiple" id="mult_opcao" name="opcao[]" style="margin-left: 0">';
                $.each(data, function(key, value){
                    html += "<option value='"+key+"'>"+value+"</option>";
                });
                html += '</select>';
                $('#ms-mult_opcao, #mult_opcao').remove();
                $('div.htmlOpcao').html(html);
                $('#mult_opcao').multiSelect({
                    selectableHeader: '<a href="#" class="btn grupo_selectAll" style="width:134px"><i class="icon-plus"></i> Marcar todos</a>',
                    selectionHeader:  '<a href="#" class="btn grupo_deselectAll" style="width:134px"><i class="icon-minus"></i> Desmarcar todos</a>',
                    afterSelect: function(values){
                        $('#mult_opcao [value="'+values+'"]').attr('selected', 'selected');
                    },
                    afterDeselect: function(values){
                        $('#mult_opcao [value="'+values+'"]').removeAttr('selected');
                    }
                  });
            },
            complete: function(){
            }
        });
    });
    //endChange
    
    //delete opções
    $('body').on('click', '.delete_atributo', function(e){
        e.preventDefault();
        var id_atributo = $(this).attr('id_atributo');
        var id_item     = $('#id_item').val();
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-purple',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir atributo",
            width: 450,
            height: 150,
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
                         "text" : "excluir",
                          click: function() {
                              $.ajax({
                                  type: "GET",
                                  url: '/material/item-opcao/delete/id_atributo/'+id_atributo+'/id_item/'+id_item,
                                  success: function(data){
                                      if(data[0].type == 'success'){
                                          $.messageBox("Dado excluido com sucesso.", 'success');
                                          $('#dialog_delete').dialog('close');
                                          $('#grid_atributo').gridOpcao();
                                      }else{
                                          $.messageBox("Não foi possivel excluir o dado.", 'error');
                                      }
                                  },
                                  error: function(){
                                      alert('Ocorreu um erro inesperado entre em contato com o administrador.');
                                  }
                              });
                        }
                }
              ]
        });
        
    });//endDeleteOpcao
    
    //edita as opções daquele atributo
    $('body').on('click', 'a.editar_atributo', function(e){
        e.preventDefault();
        var id_atributo = $(this).attr('id_atributo');
        var id_item = $('#id_item').val();
        $.ajax({
            type: "GET",
            url: '/material/item-opcao/get-by-atributo/id_atributo/'+id_atributo+'/id_item/'+id_item,
            success: function(dataOpcaoSelect){
                $.ajax({
                    type: "GET",
                    url: '/material/opcao/get-pairs-by-atributo/id_atributo/'+id_atributo,
                    success: function(data){
                        html = '<select multiple="multiple" id="mult_opcao" name="opcao[]" style="margin-left: 0">';
                        $.each(data, function(key, value){
                            html += "<option value='"+key+"'>"+value+"</option>";
                        });
                        html += '</select>';
                        $('#ms-mult_opcao, #mult_opcao').remove();
                        $('div.htmlOpcao').html(html);
                        $('#mult_opcao').multiSelect({
                            selectableHeader: '<a href="#" class="btn grupo_selectAll" style="width:134px"><i class="icon-plus"></i> Marcar todos</a>',
                            selectionHeader:  '<a href="#" class="btn grupo_deselectAll" style="width:134px"><i class="icon-minus"></i> Desmarcar todos</a>',
                            afterSelect: function(values){
                                $('#mult_opcao [value="'+values+'"]').attr('selected', 'selected');
                            },
                            afterDeselect: function(values){
                                $('#mult_opcao [value="'+values+'"]').removeAttr('selected');
                            }
                          });
                        
                        $.each(dataOpcaoSelect, function(key, value){
                            $('#mult_opcao').multiSelect('select', value.id_opcao);
                        });
                        $('#id_atributo').val(id_atributo);
                        $('a.atributo').trigger('click');
                    },
                    complete: function(){
                    }
                });
            },
            error: function(){
                alert('Ocorreu um erro inesperado entre em contato com o administrador.');
            }
        });
    });
    //endEditar
    
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
            var html =  "<option value=''>---- Selecione ----</option>";
            $.each(list, function(key, value){
                html += "<option value='"+key+"'>"+value+"</option>";
            });
            $('#combo_subgrupo').html(html);
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
            var html =  "<option value='' >---- Selecione ----</option>";
            $.each(list, function(key, value){
                html += "<option value='"+key+"'>"+value+"</option>";
            });
            $('#combo_classe').html(html);
        },
        complete: function(){
        }
    });
};

$.fn.gridOpcao = function(){
    var id_item = $('#id_item').val();
    if(!$.isNumeric( id_item )){
        return false;
    }
    var $this = $(this);
    $.ajax({
        type: "GET",
        url: '/material/item/grid-opcao/id_item/'+id_item,
        success: function(data){
            $this.html(data);
        }
    });
};
