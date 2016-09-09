$(document).ready(function(){
    //abrir o dialog de novo grupo
    $('#novoForm').click(function(e){
        e.preventDefault();
        var href= $(this).attr('href');
        $('#dialog-form').dialog({
                title:"Cadastrar grupo",
                dialogClass: 'ui-dialog-steelblue',
                modal:true,
                width: 400,
                open: function(){
                    $('form.dialog-form').attr('action', href);
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
                              "text" : "Salvar",
                               click: function() {
                                   ajaxSalvar($(this), 'grupo');
                             }
                     }
                   ],
                close: function(){
                    $('#id_grupo').val('');
                    $('#id_subgrupo').val('');
                    $('#id_classe').val('');
                    $('#nome').val('');
                    $('form.dialog-form').attr('action', '#');
                }
                });
    });

    //fechar o dialog do novo grupo
    $('a.fechar').click(function(e){
        e.preventDefault();
        $('#dialog-form').dialog('close');
    });
    
    //adicionanando uma sub-classe
    $('body').on('click','.add-subClass', function(e){
        e.preventDefault();
        var id   = $(this).attr('id');
        var href = $(this).attr('href');
        
        $('#dialog-form').dialog({
            title:"Cadastrar Sub-Grupo",
            dialogClass: 'ui-dialog-steelblue',
            modal:true,
            width: 400,
            open: function(){
                $('#id_grupo').val(id);
                $('form.dialog-form').attr('action', href);
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
                          "text" : "Salvar",
                           click: function() {
                               ajaxSalvar($(this),'sub-grupo');
                         }
                 }
               ],
            close: function(){
                $('#id_grupo').val('');
                $('#id_subgrupo').val('');
                $('#id_classe').val('');
                $('#nome').val('');
                $('form.dialog-form').attr('action', '#');
            }
            
            });
    });
    
    //adicionanando uma classe
    $('body').on('click','.add-class', function(e){
        e.preventDefault();
        var id = $(this).attr('id');
        var href = $(this).attr('href');
        
        $('#dialog-form').dialog({
            title:"Cadastrar Classe",
            modal:true,
            dialogClass: 'ui-dialog-steelblue',
            width: 400,
            open: function(){
                $('#id_subgrupo').val(id);
                $('form.dialog-form').attr('action', href);
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
                          "text" : "Salvar",
                           click: function() {
                               ajaxSalvar($(this),'classe');
                         }
                 }
               ],
            close: function(){
                $('#id_grupo').val('');
                $('#id_subgrupo').val('');
                $('#id_classe').val('');
                $('#nome').val('');
                $('form.dialog-form').attr('action', '#');
            }
            });
    });
    
    
    $('form').submit(function(){
        return false;
    });

    //Árvore de grupo, subgrupo e classe
    $("#tree").treeview({
        collapsed: true,
        animated: "medium",
        control:"#sidetreecontrol",
        persist: "location"
    });
    
    
    $('body').on('click', '.hitarea-create',function(){
        $(this).parent().children('ul').toggle('slow');
        $(this).toggleClass("expandable-hitarea");
        $(this).toggleClass("collapsable-hitarea");
    });
    
    $('body').on('click', 'a.edit', function(e){
        e.preventDefault();
        var id_grupo     = $(this).attr('id_grupo');
        var id_subgrupo  = $(this).attr('id_subgrupo');
        var id_classe    = $(this).attr('id_classe');
        var href         = $(this).attr('href');
        var type         = $(this).attr('type');
        var nome         = $(this).parent().siblings('span').text().trim();
        var nome_span    = $(this).parent().siblings('span');
        var $this        = $(this);
        
        $('#dialog-form').dialog({
            title:"Editar "+type,
            modal:true,
            dialogClass: 'ui-dialog-steelblue',
            width: 400,
            open: function(){
                $('#id_grupo').val(id_grupo);
                $('#id_subgrupo').val(id_subgrupo);
                $('#id_classe').val(id_classe);
                $('form.dialog-form').attr('action', href);
                $('#nome').val(nome);
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
                          "text" : "Salvar",
                           click: function() {
                               $this.editarSalvar($(this),type, nome_span);
                         }
                 }
               ],
            close: function(){
                $('#id_grupo').val('');
                $('#id_subgrupo').val('');
                $('#id_classe').val('');
                $('#nome').val('');
                $('form.dialog-form').attr('action', '#');
            }
            });
    });
    
    $('body').on('click', 'a.deleteMoldal', function(e){
        e.preventDefault();
        var href = $(this).attr("href");
        var name = $(this).parent().siblings('span').text().trim();
        $("#valueDelete").prepend(name);
        $('#dialog_delete').dialog({
            modal: true,
            dialogClass: 'ui-dialog-steelblue',
            position: [($(window).width() / 2) - (450 / 2), 200],
            resizable: true,
            title: "Excluir",
            width: 450,
            height: 200,
            buttons: [
                      {
                         'class' : 'btn gree',
                         "text" : "Cancelar",
                        click: function() {
                          $( this ).dialog( "close" );
                        }
                      },
                      {
                         'class' : 'btn red',
                         "text" : "Excluir",
                          click: function() {
                              window.location.href = href;
                        }
                }
              ],
              close: function(){
                  $("#valueDelete").empty();
              }
        });
        
    });
});

// ao clicar em salvar ira salvar via ajax os dados para um grupo ou novo grupo
function ajaxSalvar($dialog, type){
    var nome = $('#nome').val();
    var url = $('form.dialog-form').attr('action');
    if(nome == ""){
        alert("O campo nome está vazio.");
        return false;
    }
    var data = {nome        : nome,
                id_grupo    : $('#id_grupo').val(),
                id_subgrupo : $('#id_subgrupo').val(),
                id_classe   : $('#id_classe').val()
                };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(e){
            if(e.success){
                addTree(e, type);
                $.messageBox("Dado inserido com sucesso.", 'success');
                $dialog.dialog('close');
            }else{
                alert('Não foi possivel salvar no momento.');
            }
        },
        error: function(e){
            alert("Sistema está fora do ar entre em contato com o administrador.");
        }
    });
}

//ao clicar em salvar ira salvar via ajax os dados para um grupo ou novo grupo
$.fn.editarSalvar = function ($dialog, type, nome_span){
    var id = $(this).attr('id');
    var nome = $('#nome').val();
    var url = $('form.dialog-form').attr('action');
    if(nome == ""){
        alert("O campo nome está vazio.");
        return false;
    }
    if($('#id_grupo').val() != ""){
        
    }
    var data = {nome        : nome,
                id_grupo    : $('#id_grupo').val(),
                id_subgrupo : $('#id_subgrupo').val(),
                id_classe   : $('#id_classe').val()
                };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(e){
            if(e.success){
                $.messageBox("Dado atualizado com sucesso.", 'success');
                nome_span.text(nome);
                $dialog.dialog('close');
            }else{
                alert('Não foi possivel editar no momento.');
            }
        },
        error: function(e){
            alert("Sistema está fora do ar entre em contato com o administrador.");
        }
    });
};

function addTree(data, type){
    var branches = null;
    if( type == "grupo"){
        branches = $(
			  "<li class='class id_grupo_"+data.id+"'><span class='grupo_"+data.id+"'>"+data.nome+"</span>"+
              	"<div class='action' > "+
              		"<a id_grupo='"+data.id+"' href='/service/grupo/form' class='edit'>"+
              		"<i class='icon-pencil' title='editar Grupo'></i>"+
          			"</a>"+
          			"<a id='"+data.id+"' href='/service/sub-grupo/form' class='add-subClass'>"+
          				'<i class="icon-reorder" title="adicionar sub-Grupo"></i>'+
      				'</a>'+
      				'<a id="'+data.id+'" href="/service/item/grid/id_grupo/'+data.id+'" class="add add_action_grupo_'+data.id+'">'+
      					"<i class='icon-cogs' title='Serviço'></i>"+
              		"</a></div></li>").appendTo("#tree");
    } else if(type == "sub-grupo") {
          var id_grupo = $('#id_grupo').val();
          if($(".id_grupo_"+id_grupo+" > ul").length > 0){
              branches = $("<li class='class id_subgrupo_"+data.id+"'><span class='subgrupo_"+data.id+"'>"+data.nome+"</span>"+
                      "<div class='subAction' >"+
                      " <a id_subgrupo='"+data.id+"' href='/service/sub-grupo/form' id_grupo='"+id_grupo+"' class='edit'>"+
                      "<i class='icon-pencil' title='editar Sub-Grupo'></i>"+
                      "</a>"+
                      " <a id='"+data.id+"' href='/service/classe/form' class='add-class'>"+
                      '<i class="icon-reorder" title="adicionar Classe"></i>'+
                      '</a> <a id="'+data.id+'" href="/service/item/grid/id_subgrupo/'+data.id+'" class="add add_action_subgrupo_'+data.id+'">'+
                      "<i class='icon-cogs' title='Serviço'></i>"+
                      "</a></div></li>").appendTo(".id_grupo_"+id_grupo+" > ul");
          }else {
              var hitarea = $('<div class="hitarea collapsable-hitarea hitarea-create"></div>').prependTo(".id_grupo_"+id_grupo);
              branches = $("<ul><li class='class id_subgrupo_"+data.id+"'><span class='subgrupo_"+data.id+"'>"+data.nome+"</span>"+
                      "<div class='subAction' > "+
                      " <a id_subgrupo='"+data.id+"' href='/service/sub-grupo/form' id_grupo='"+id_grupo+"' class='edit'>"+
                      	"<i class='icon-pencil' title='editar Sub-Grupo'></i>"+
                      "</a>"+
                      " <a id='"+data.id+"' href='/service/classe/form' class='add-class'>"+
                      '<img src="/images/tab-add-icon.png" data-tooltip title="adicionar classe"/> '+
                      '</a> <a id="'+data.id+'" href="/service/item/grid/id_subgrupo/'+data.id+'" class="add add_action_subgrupo_'+data.id+'">'+
                      "<i class='icon-cogs' title='Serviço'></i>"+
                      "</a></div></li></ul>").appendTo(".id_grupo_"+id_grupo);
              $("#tree").treeview({
                  add: hitarea
              });
          }
          //Remove a opção de adicionar um item
          $('.add_action_grupo_'+id_grupo).remove();
    }else if(type == "classe") {
        var id_subgrupo = $('#id_subgrupo').val();
        if($(".id_subgrupo_"+id_subgrupo+" > ul").length > 0){
            branches = $("<li class='class id_classe_"+data.id+"'><span class='classe_"+data.id+"'>"+data.nome+"</span>"+
                    "<div class='classe' >"+
                    " <a id_classe='"+data.id+"' href='/service/classe/form' id_subgrupo='"+id_subgrupo+"' class='edit'>"+
                        "<i class='icon-pencil' title='editar Classe'></i>"+
                    "</a>"+
                    ' <a id="'+data.id+'" href="/service/item/grid/id_classe/'+data.id+'" class="add">'+
                    	"<i class='icon-cogs' title='Serviço'></i>"+
                    "</a></div></li>").appendTo(".id_subgrupo_"+id_subgrupo+" > ul");
        }else {
            var hitarea = $('<div class="hitarea collapsable-hitarea hitarea-create"></div>').prependTo(".id_subgrupo_"+id_subgrupo);
            branches = $("<ul><li class='class id_classe_"+data.id+"'><span class='classe_"+data.id+"'>"+data.nome+"</span>"+
                    "<div class='classe' >"+
                    " <a id_classe='"+data.id+"' href='/service/classe/form' id_subgrupo='"+id_subgrupo+"' class='edit'>"+
                        "<i class='icon-pencil' title='editar Classe'></i>"+
                    "</a>"+
                    ' <a id="'+data.id+'" href="/service/item/grid/id_classe/'+data.id+'" class="add">'+
                    "<i class='icon-cogs' title='Serviço'></i>"+
                    "</a></div></li></ul>").appendTo(".id_subgrupo_"+id_subgrupo);
            $("#tree").treeview({
                add: hitarea
            });
        }
        //Remove a opção de adicionar um item
        $('.add_action_subgrupo_'+id_subgrupo).remove();
  }
    $("#tree").treeview({
        add: branches
    });
}