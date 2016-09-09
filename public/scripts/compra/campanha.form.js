
$(document).ready(function(){
    $('#porcent_comissao, #porcent_multa').setMask('999');
    $('#qtd_compra').setMask('integer');
    $('#vl_min_compra, #vl_max_compra, #vl_adicional').setMask('decimal');
    $('.sigla_moeda').moeda();
    $('#tab-produto').gridproduto();
    $('#dt_inicio, #dt_fim').setMask({mask : '99/99/9999 99:99', autoTab: false });
    
    //aparece o calendário no campo data fim
    $("#dt_inicio").datetimepicker({
        format: "dd/mm/yyyy hh:ii",
        language: 'pt-BR',
        autoclose: true,
        todayBtn: true,
        pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
        minuteStep: 5,
    }).on('changeDate', function(ev){
        $('#dt_fim').datetimepicker('setStartDate', Date.parse($('#dt_inicio').val()));
    });
    //aparece o calendário no campo data fim
    $("#dt_fim").datetimepicker({
        format: "dd/mm/yyyy hh:ii",
        autoclose: true,
        todayBtn: true,
        language: 'pt-BR',
        linkFormat: "yyyy-mm-dd hh:ii",
        pickerPosition: (App.isRTL() ? "bottom-right" : "bottom-left"),
        minuteStep: 5,
    }).on('changeDate', function(ev){
        $('#dt_inicio').datetimepicker('setEndDate', Date.parse($('#dt_fim').val()));
    });
    if($('#dt_fim').val() != '' && $('#dt_inicio').val() != ''){
        $('#dt_fim').datetimepicker('setStartDate', Date.parse($('#dt_inicio').val()));
        $('#dt_inicio').datetimepicker('setEndDate', Date.parse($('#dt_fim').val()));
    }
    
    //muda a sigla da moeda
    $('#id_moeda').change(function(){
    	$('.sigla_moeda').moeda();
    });
    
    //verifica se a porcentagem e maior
    $('#porcent_comissao, #porcent_multa,#porcent_comissao_cons').change(function(){
    	if($(this).val() > 100){
    		$(this).val('100');
    	}
    });
    
    //adicionando produto
    $('.add_produto').click(function(e){
    	e.preventDefault();
        $('.dialog_pesquisa_produto').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Pesquisar produto",
            width: 450,
            height: 280,
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
                         "text" : "Pesquisar",
                          click: function() {
                            $.ajax({
                                type: "POST",
                                url: '/material/item/find-produto-json',
                                data: {id_classe: $('#id_classe').val(), id_subgrupo:$('#id_subgrupo').val(), 
                                       nome: $('#nome_item').val(), referencia:$('#referencia').val(),
                                       id_grupo: $('#id_grupo').val()},
                                success: function(data){
                                    if(data.count == 0){
                                        return;
                                    }
                                    var html = "";
                                    $.each(data.list, function(key, value){
                                        var valor_revenda = value.valor_revenda != null ? value.valor_revenda.replace('.', ',') : value.valor_revenda;
                                        html += "<tr>";
                                        html += "<td><input type='checkbox' name='id_item[]' value='"+value.id_item+"' class='checkboxitem' ></td>";
                                        html += "<td><input type='text' value='"+value.nome+"' disabled='disabled' ></td>";
                                        html += "<td><input type='hidden' name='nome_item_"+value.id_item+"' value='"+value.nome+"'></td>";
                                        html += "<td><input type='text' name='valor_"+value.id_item+"' class='decimal span2' value='"+valor_revenda+"' ></td>";
                                        html += "</tr>";
                                    });
                                    $('table.table-produto tbody').html(html);
                                    $('.decimal').setMask('decimal');
                                    $('.checkboxitem').uniform();
                                    $('.dialog-produto').dialog({
                                        modal: true,
                                        dialogClass: 'ui-dialog-green',
                                        position: [($(window).width() / 2) - (450 / 2), 200],
                                        resizable: true,
                                        title: "Adicionar produto ("+data.count+")",
                                        width: 550,
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
                                                          if(validarAdicionarProduto()){
                                                              return;
                                                          }
                                                          $.ajax({
                                                              type: "POST",
                                                              url: '/compra/campanha-item/form-by-campanha/id_campanha/'+$('#id_campanha').val(),
                                                              data: $('#formProduto').serialize(),
                                                              success: function(data){
                                                                  if(data.success){
                                                                      alert('Produto inserido na campanha com sucesso.');
                                                                      $('.dialog-produto').dialog('close');
                                                                      $('#tab-produto').gridproduto();
                                                                  }else{
                                                                      alert(data.mensagem[0].text);
                                                                  }
                                                              },
                                                              complete: function(){
                                                              }
                                                          });
                                                    }
                                            }
                                          ],
                                          close: function(){
                                                $('table.table-produto tbody').html('');
                                                $('.check-all-produto').prop('checked', false).uniform();
                                          }
                                    });
                                    
                                }
                            });
                        }
                }
              ],
              close: function(){
                    $('#id_grupo').val('');
                    $('#id_grupo').selectSubGrupo();
                    $('#id_subgrupo').selectClasse();
                    $('#nome_item').val('');
                    $('#referencia').val('');
              },
              open: function(){
            	  $('.filtro_nome, .filtro_referencia').css('display', 'none');
            	  $('.filtro_grupo').css('display', 'block');
                  $('#id_grupo').selectSubGrupo();
                  $('#id_subgrupo').selectClasse();
              }
        });
    });
    
    //seleciona todos os checkbox
    $('body').on('click', '.check-all-produto', function(){
        if($(this).is(':checked')){
            $('.checkboxitem').prop('checked', true).uniform();
        }else{
            $('.checkboxitem').prop('checked', false).uniform();
        }
        $('.dialog-produto').dialog( "option", "title", "Adicionar produto ("+$('.checkboxitem').length+") - selecionado ("+$('.checkboxitem').filter(':checked').length+")");
    });

    $('body').on('click', '.checkboxitem', function(){
        $('.dialog-produto').dialog( "option", "title", "Adicionar produto ("+$('.checkboxitem').length+") - selecionado ("+$('.checkboxitem').filter(':checked').length+")");
    });
    
    //ao mudar o filtro irá mostrar input diferente
    $('body').on('change', '#id_filtro', function(){
    	var value = $(this).val();
    	$('#id_grupo').val('');
    	$('#id_grupo').selectSubGrupo();
    	$('#id_subgrupo').selectClasse();
        $('#nome_item').val('');
        $('#referencia').val('');
    	if(value == 0){
    		$('.filtro_grupo').css('display', 'block');
    		$('.filtro_nome, .filtro_referencia').css('display', 'none');
    	}else if(value == 1){
    		$('.filtro_referencia').css('display', 'block');
    		$('.filtro_nome, .filtro_grupo').css('display', 'none');
    	}else if(value == 2){
    		$('.filtro_nome').css('display', 'block');
    		$('.filtro_referencia, .filtro_grupo').css('display', 'none');
    	}
    	
    });
    
    //carrega o select do subgrupo assim que mudar o select do id_grupo
    $('body').on('change', '#id_grupo', function(){
    	$(this).selectSubGrupo();
    });
    //carrega o select da classe assim que mudar o select do id_subgrupo
    $('body').on('change', '#id_subgrupo', function(){
    	$(this).selectClasse();
    });
    
    //delete produto
    $('body').on('click', '.delete_produto', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('#dialog-delete-produto').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir produto",
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
                                  url: href,
                                  success: function(data){
                                      if(data[0].type == 'success'){
                                          $.messageBox("Dado excluido com sucesso.", 'success');
                                          $('#dialog-delete-produto').dialog('close');
                                          $('#tab-produto').gridproduto();
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
        
    });//endDeleteProduto
    
    //edit produto
    $('body').on('click', '.edit_produto', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        var id_campanha_item = $(this).attr('id_campanha_item');
        $('#dialog_edit_produto').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir produto",
            width: 400,
            height: 280,
            open: function(){
                $.ajax({
                    type: "GET",
                    url: '/compra/campanha-item/get/id_campanha_item/'+id_campanha_item,
                    success: function(data){
                        $('#id_campanha_item').val(data.id_campanha_item);
                        $('#vl_unitario').val(data.vl_unitario.replace('.', ','));
                        $('#edit_produto').val(data.nome_produto);
                        $('#vl_unitario').setMask('decimal');
                    },
                    error: function(){
                        alert('Ocorreu um erro inesperado entre em contato com o administrador.');
                    }
                });
            },
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
                         "text" : "Atualizar",
                          click: function() {
                              $.ajax({
                                  type: "POST",
                                  url: href,
                                  data: $('#form-edit-produto').serialize(),
                                  success: function(data){
                                      if(data.success){
                                          alert('Produto atualizado com sucesso.');
                                          $('#dialog_edit_produto').dialog('close');
                                          $('#tab-produto').gridproduto();
                                      }else{
                                          alert(data.mensagem[0].text);
                                      }
                                  },
                                  error: function(){
                                      alert('Ocorreu um erro inesperado entre em contato com o administrador.');
                                  }
                              });
                        }
                }
              ],
            close: function(){
                $('#id_campanha_item').val('');
                $('#vl_unitario').val('');
                $('#edit_produto').val('');
            }
        });
        
    });//endDeleteProduto
});

$.fn.moeda = function(){
	var id_moeda = $('#id_moeda').val();
	var $this = $(this);
	$.ajax({
        type: "POST",
        url: '/financial/moeda/get',
        data: {id_moeda : id_moeda},
        success: function(data){
        	$this.text(data.moe_sigla);
        }
    });
};
$.fn.selectSubGrupo = function(){
    var id_grupo = $(this).val();
    if(id_grupo == ""){
        var html =  "<option value=''>---- Selecione ----</option>";
        $('#id_subgrupo').html(html);
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
            $('#id_subgrupo').html(html);
        },
        complete: function(){
        }
    });
};

$.fn.selectClasse = function(){
    var id_subgrupo = $(this).val();
    if(id_subgrupo == ""){
        var html =  "<option value='' >---- Selecione ----</option>";
        $('#id_classe').html(html);
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
            $('#id_classe').html(html);
        },
        complete: function(){
        }
    });
};

function validarAdicionarProduto(){
    var retorno = false;
    if($('.checkboxitem').filter(':checked').length == 0){
        alert('Selecione ao menos um produto.');
        return true;
    }
    $('.checkboxitem').filter(':checked').each(function( index ) {
        var id_item = $(this).val();
        if($('input[name="valor_'+id_item+'"]').val() == '0,00' && retorno == false){
            retorno = true;
            return false;
        }
    });
    if(retorno && !confirm('Existe produto com valores zerado, gostaria de continuar?')){
        retorno = true;
    }else{
        retorno = false;
    }
    return retorno;
    
}
$.fn.gridproduto = function(){
    var id_campanha = $('#id_campanha').val();
    var $this       = $(this);
    if(id_campanha == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: '/compra/campanha-item/grid-campanha/id_campanha/'+id_campanha,
        success: function(data){
            $this.html(data);
        },
        complete: function(){
        }
    });
};