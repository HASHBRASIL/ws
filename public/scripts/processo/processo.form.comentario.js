$('document').ready(function(){
    $.fn.modal.Constructor.prototype.enforceFocus = function () {};
    //chama a tabela do comentário
    $('#grid-comentario').tableComentario();
    $('body').on('click', 'i.comentario', function(){

        var id_empresa = $(this).parent().siblings('div.controls').find('#empresas_id, .id_empresa_entrega').val();
        if(id_empresa == ""){
            return;
        }
        $("#dialog-comentario").dialog({
            modal: true,
            title: "Comentário",
            position: [($(window).width() / 2) - (500 / 2), 200],
            width: "500",
            height: "560",
            dialogClass: "ui-dialog-caribbean-green",
            open: function(event, ui) {
                $("#dialog-comentario").gridComentario(id_empresa);
                $('#grid-comentario').tableComentario();
                $('.scroller').slimScroll({
                    scrollTo: $('ul.chats').height(),animate:true
                });
            },
            close: function(){
                $("#dialog-comentario").html('');
            }
        });
    });

    $('body').on('click', 'i.edit-chat', function(){
        $('li.in').removeClass('edit-text');
        $(this).parent().parent().addClass('edit-text');
        var id_comentario = $(this).attr('id-comentario');
        $.getJSON('processo/comentario/get/id_comentario/'+id_comentario, function(comentario){
            $('#descricao_chat').val(comentario.descricao).attr('style', 'background-color:#c3dafe !important');
            $('#id_comentario').val(comentario.id_comentario);
        });
    });


    $('body').on('click', 'i.delete-chat', function(e){
        e.preventDefault();
        var id_comentario = $(this).attr('id-comentario');
        $('#btn-excluir-comentario').removeAttr('href');
        $('#btn-excluir-comentario').attr('href', 'processo/comentario/delete/id_comentario/'+id_comentario);
        $('#delete-comentario').modal('show');

    });

    $('body').on('click','#btn-excluir-comentario', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        $.ajax({
            type: "GET",
            url: href,
            success: function(data){
                if( data[0].type =="success"){
                    alertModal('Comentario excluído com sucesso.');
                    $('#btn-excluir-comentario').removeAttr('href');
                    $('#delete-comentario').modal('hide');
                    $("#dialog-comentario").gridComentario($('#id_empresa_chat').val());
                    $('#grid-comentario').tableComentario();
                    $('.scroller').slimScroll({
                        scrollTo: $('ul.chats').height(),animate:true
                    });
                }else{
                    alertModal(data[0].text);
                }
            },
            error: function(){
                alertModal("Serviço temporariamente indisponivel. entre em contato com o administrador.");
            }
        });
    });

    $('body').on('keyup', '#descricao_chat', function (event) {
        var key = event.keyCode || event.which;console.log(key);

        if (key === 13) {
            $('a.button-chat').trigger('click');
        }
        return false;
    });

    $('body').on('click', 'a.button-chat',function(e){
        e.preventDefault();
        if($('#descricao_chat').val() == ""){
            return;
        }
        $.ajax({
            type: "POST",
            url: "processo/comentario/form",
            data: {'id_processo': $('#pro_id').val(), 'id_corporativa': $('#id_empresa_chat').val(), 'descricao':$('#descricao_chat').val(), 'id_comentario': $('#id_comentario').val()},
            beforeSend: function(){
                $("#load").show();
            },
            success: function(data){
                if(data.success == false){
                    alertModal('Não foi possivel salvar.');
                    return;
                }
                $.getJSON('processo/comentario/get/id_comentario/'+data.id.id_comentario, function(comentario){
                    var html = '<li class="in">'+
                        '<img class="avatar" alt="" src="assets/img/avatar.png" />'+
                        '<div class="message">'+
                            '<span class="arrow"></span>'+
                            '<a href="#" class="name">'+comentario.nome_usuario+'</a> '+
                            '<span class="datetime">'+comentario.data_format+'</span>'+
                            '<i class="icon-pencil edit-chat" title="Editar" id-comentario="'+comentario.id_comentario+'" ></i>'+
                            ' <i class="icon-remove delete-chat" title="Excluir" id-comentario="'+comentario.id_comentario+'" ></i>'+
                            '<span class="body">'+
                                comentario.descricao+
                            '</span>'+
                        '</div>'+
                    '</li>';
                    if($('#id_comentario').val() ==""){
                        $(html).appendTo('ul.chats');
                        $('.scroller').slimScroll({
                            scrollTo: $('ul.chats').height(),animate:true
                        });
                    }else{
                        $('li.in.edit-text div span.body').text(comentario.descricao);
                    }

                    $('#descricao_chat').val('');
                    $('#id_comentario').val('');
                    $('#descricao_chat').removeAttr('style');
                    $('li.in').removeClass('edit-text');
                });

            },
            complete: function(){
                $("#load").hide();
            },
            error: function(){
                alertModal("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err1");
            }

        });
    });
});

$.fn.gridComentario = function(id_empresa){
    var id_processo= $('#pro_id').val();
    var $this = $(this);
    /* se quem estiver logado for uma empresa não irá mostrar a grid */
    if(id_processo == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: "processo/comentario/chat/id_empresa/"+id_empresa+"/id_processo/"+id_processo,
        beforeSend: function(){
            $("#load").show();
        },
        success: function(data){
            $this.html(data);
            $('.scroller').slimScroll({
                scrollTo: $('ul.chats').height(),animate:true
            });
        },
        complete: function(){
            $("#load").hide();
        },
        error: function(){
            alertModal("Serviço temporariamente indisponivel. entre em contato com o administrador. Ajx err1");
        }

    });
};

$.fn.tableComentario = function(id_empresa){
    var id_processo= $('#pro_id').val();
    var $this = $(this);
    /* se quem estiver logado for uma empresa não irá mostrar a grid */
    if(id_processo == ""){
        return;
    }
    $.ajax({
        type: "GET",
        url: "processo/comentario/grid-by-processo/id_processo/"+id_processo,
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

function alertModal(text){
    $('#alert-modal div.modal-body p').text(text);
    $('#alert-modal').modal('show');
}