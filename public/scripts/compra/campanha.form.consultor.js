$(document).ready(function(){
    $('#tab-consultor').gridConsultor();
    //adicionando produto
    $('a.add_consultor').click(function(e){
        e.preventDefault();
        $('#dialog_consultor').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Pesquisar consultor",
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
                              var id_corporativa = $('#id_corporativa').val();
                              var tipo_pessoa = $('#tipo_pessoa').val();
                              if(id_corporativa == ""){
                                  alert('Selecione um consultor.');
                                  return;
                              }
                              $.ajax({
                                  type: "GET",
                                  url: '/compra/campanha-corporativo/tree-consultor/id_corporativo/'+id_corporativa+'/tipo_pessoa/'+tipo_pessoa,
                                  success: function(data){
                                      $('#form-tree-consultor').html(data);
                                      $('.checkbox-consultor').uniform();
                                      //Árvore de consultores
                                      $("#tree").treeview({
                                          collapsed: true,
                                          animated: "medium",
                                          control:"#sidetreecontrol",
                                          persist: "location"
                                      });
                                      
                                      $('#tree-consultor').dialog({
                                          modal: true,
                                          dialogClass: 'ui-dialog-green',
                                          position: [($(window).width() / 2) - (450 / 2), 200],
                                          resizable: true,
                                          title: "Cadastrar consultor",
                                          width: 500,
                                          height: 580,
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
                                                        "text" : "Cadastrar",
                                                        click: function() {
                                                          $.ajax({
                                                              type: "POST",
                                                              url: '/compra/campanha-corporativo/form-by-campanha/id_campanha/'+$('#id_campanha').val(),
                                                              data: $('#form-tree-consultor').serialize(),
                                                              success: function(data){
                                                                  if(data.success){
                                                                      alert('Consultores inserido na campanha com sucesso.');
                                                                      $('#tree-consultor').dialog('close');
                                                                      $('#tab-consultor').gridConsultor();
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
                                                  $('#form-tree-consultor').html('');
                                                  $('#id_corporativa').val('');
                                                  $('#nome_consultor').val('');
                                            }
                                      });
                                  },
                                  complete: function(){
                                  }
                              });
                              
                        }
                }
              ],
              close: function(){
                    $('#id_corporativa').val('');
                    $('#nome_consultor').val('');
              },
              open: function(){
                  $( "#nome_consultor" ).autocomplete({
                      source: "/empresa/empresa/autocomplete-geral/tps_id/"+$('#tipo_pessoa').val(),
                      minLength: 2,
                      select: function( event, ui ) {
                          $('#id_corporativa').val(ui.item.id);
                      },
                      search: function( event, ui ) {
                          $('#id_corporativa').val("");
                      }
                    });
              }
        });
    });
    
    $('body').on('click','.checkbox-consultor', function(){
        if($(this).is(':checked') && $(this).parent().parent().parent().parent().find('input').length > 1){
            if(confirm("Deseja marcar todos a baixo?")){
                $(this).parent().parent().parent().parent().find('input').prop('checked', true);
                $('.checkbox-consultor').uniform();
            }
        }else if(!$(this).is(':checked') && $(this).parent().parent().parent().parent().find('input').length > 1){
            if(confirm("Deseja desmarcar todos a baixo?")){
                $(this).parent().parent().parent().parent().find('input').prop('checked', false);
                $('.checkbox-consultor').uniform();
            }
        }
    });
    
    $('body').on('click', '.edit_consultor', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        var id_campanha_corporativa = $(this).attr('id_campanha_corporativa');
        $('#dialog-edit-consultor').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (500 / 2), 200],
            resizable: true,
            title: "Excluir produto",
            width: 500,
            height: 370,
            open: function(){
                $.ajax({
                    type: "GET",
                    url: '/compra/campanha-corporativo/get/id_campanha_corporativa/'+id_campanha_corporativa,
                    success: function(data){
                        $('#edit_consultor').val(data.nome_consultor);
                        $('#id_campanha_corporativa').val(data.id_campanha_corporativa);
                        $('#id_tp_comissao_cons').val(data.id_tp_comissao);
                        $('#porcent_comissao_cons').val(data.porcent_comissao);
                        $('#vl_max_compra_cons').val(data.vl_max_compra);
                        $('#qtd_compra_cons').val(data.qtd_compra);
                        
                        $('#porcent_comissao_cons').setMask('999');
                        $('#qtd_compra_cons').setMask('integer');
                        $('#vl_max_compra_cons').setMask('decimal');
                        
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
                                  data: {id_tp_comissao : $('#id_tp_comissao_cons').val(), porcent_comissao : $('#porcent_comissao_cons').val(),
                                      vl_max_compra : $('#vl_max_compra_cons').val(), qtd_compra : $('#qtd_compra_cons').val() },
                                  success: function(data){
                                      if(data.success){
                                          alert('Consultor atualizado com sucesso.');
                                          $('#dialog-edit-consultor').dialog('close');
                                          $('#tab-consultor').gridConsultor();
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
        
    });
    
    //delete consultor
    $('body').on('click', '.delete_consultor', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $('#dialog-delete-consultor').dialog({
            modal: true,
            dialogClass: 'ui-dialog-green',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir consultor",
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
                                          $('#dialog-delete-consultor').dialog('close');
                                          $('#tab-consultor').gridConsultor();
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
        
    });//endDeleteConsultor
});

$.fn.gridConsultor = function(){
    var id_campanha = $('#id_campanha').val();
    var $this       = $(this);
    if(id_campanha == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: '/compra/campanha-corporativo/grid-campanha/id_campanha/'+id_campanha,
        success: function(data){
            $this.html(data);
        },
        complete: function(){
        }
    });
};