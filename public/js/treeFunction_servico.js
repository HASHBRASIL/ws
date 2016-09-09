var to = false;
        
$(document).ready(function() 
{
    function refreshTree()
    {
        $('#jstree').jstree(true).refresh();
        abreLoader();
    }

    $('body').on('change', '.grupoDivPropriedades table input[type="text"]', function(e){
        e.preventDefault();
        var data = {'id' : $('#iditem').val(), 'coluna' : $(this).attr('coluna'), 'valor' : $(this).val()};
        abreLoader();
 
        var buttonEvent = data['coluna'] === 'nome' ? 0 : 2;

        $.ajax({ // -- HASH_CONTENT_SERVICO_SALVA_PROP_TREE
            url     : location.pathname+'?servico=0803e49c-5900-11e6-b6c7-6745f7c1d8a7',
            data    : data,
            dataType : 'json',
            method  : 'post',
            success : function(data){
                fechaLoader();
                data.msg === ''
                    ?selecionaNode('', {event: {button: buttonEvent}, node: {id: $('#iditem').val()}})
                    :alert(data.msg);
            },
            error : function(){
                alert('erro');
                fechaLoader();
            }
        });
    });

    $('body').on('click', '.grupoDivMetadados button', function(e)
    {
        e.preventDefault();
        var that = this;
        bootbox.confirm('Tem certeza que quer remover o metadado?', function(confirmacao){
            if (confirmacao) {
                var data = {
                    idmetadado: $(that).attr('data-idmetadado')
                };
                abreLoader();
                $.ajax({ // -- HASH_CONTENT_SERVICO_REM_META_TREE
                    url: location.pathname+'?servico=ebd0195a-c542-4e81-d6ea-f959ad12636c',
                    data: data,
                    dataType: 'json',
                    method: 'post',
                    success: function(data){
                        if ('' === data.msg) {
                            $(that).parents('tr').remove();
                        } else {
                            alert(data.msg);
                        }
                        fechaLoader();
                    },
                    error : function(){
                        alert('erro');
                        fechaLoader();
                    }
                });
            }
        });
    });

    $('body').on('submit', '.grupoDivMetas form', function(e){
        e.preventDefault();
        var data = $('#filtros').serialize()
            + '&iditem=' + $('#iditem').val()
            + '&id=' + $('#id_metanome_filtro').val();
        abreLoader();        
        $.ajax({ // -- HASH_CONTENT_SERVICO_ADIC_FILTRO_TREE
            url: location.pathname+'?servico=f0ed19f4-6739-4bbf-aedc-997e86564757',
            data: data,
            dataType: 'json',
            method: 'post',
            success: function(data){
                $('#id_metanome_filtro').val(data.id);
                fechaLoader();
                data.msg === ''
                    ?''
                    :alert(data.msg);
            },
            error: function(){
                alert('erro');
                fechaLoader();
            }
        });
    });

    $('body').on('submit', '.grupoDivMetadados form', function(e){
        e.preventDefault();
        var data = $('#metadados').serialize()
            + '&iditem=' + $('#iditem').val();
        abreLoader();        
        $.ajax({ // -- HASH_CONTENT_SERVICO_ADIC_META_TREE
            url: location.pathname+'?servico=5598b917-bca7-4dd2-a9d3-56c252081241',
            data: data,
            dataType: 'json',
            method: 'post',
            success: function(data){
                fechaLoader();
                data.msg === ''
                    ?''
                    :alert(data.msg);
            },
            error: function(){
                alert('erro');
                fechaLoader();
            }
        });
    });
    
    $('.form-tree').submit(function(e){
       e.preventDefault();
       refreshTree();
    });

    $('.busca-tree').keyup(function () {
        if(to) { clearTimeout(to); }
        to = setTimeout(function () {
            var v = $('.busca-tree').val();
            $('#jstree').jstree(true).search(v);
        }, 250);
    });
 
    $('#jstree').jstree({
        "checkbox": {
            "keep_selected_style": true
        },
        "core" : {
            "themes" : { "stripes" : true },
            "check_callback": beforeCallback,
            'data' : {
                'url':function (node) { // -- HASH_CONTENT_SERVICO_LISTA_TREE
                    return location.pathname
                           + '?servico=654cb986-55da-11e6-8990-eb8111e822e6&'
                           + $('.form-tree').serialize();
                  },
                  "callback": function(){alert(42)},
                  "data_callback": function(){alert(444)},
              }
        },
        "types" : {
            "#" : {"max_depth" : 5, "valid_children" : ["root"]},
            "root" : {"icon" : "glyphicon glyphicon-file", "valid_children" : ["detalhe", "root"]},
            "default" : {'icon' : "fa fa-folder-o"},
            "site" : {'icon' : "fa fa-sitemap"}
        },
        "plugins" : [
            "dnd", "contextmenu", "search", "state", "types", "json_data"
        ]
    }).on('paste.jstree', function(e, data){    
        //evento para colar
    }).on('select_node.jstree', selecionaNode)
    .on("move_node.jstree", function (e, data) {
        var objeto = {  
                        'uuid' : data.node.id,
                        'idPai' : data.node.parent,
                        'reordenarGrupos' : [
                            pegaListaFilhos(data.node.old_parent),
                            pegaListaFilhos(data.node.parent)
                        ]
                    };
        salvar(objeto);
    })
    .on("refresh.jstree", function () {
        fechaLoader();
    });


    function selecionaNode(e, data)
    {
        if(data !== undefined && data.event !== undefined && data.event.button !== 2){
            $('.editar-propriedade').html('<i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>');
            abreLoader();
            $.ajax({ // -- HASH_CONTENT_SERVICO_SHOW_PROP_TREE
                url: location.pathname+'?servico=6958913a-8e14-431a-c657-8363bd8a1a31',
                data: {uuid : data.node.id},
                method: 'post',
                success: function(data){
                    $('.editar-propriedade').html(data);
                    fechaLoader();
                },
                error: function(){
                    $('.editar-propriedade').html('Erro...');
                    fechaLoader();
                }
            });
        }
    }

    function beforeCallback(nomeEvento, param1, param2, param3)
    {
        console.log(nomeEvento);
        if ('delete_node' === nomeEvento){
            if (confirm('Deseja deletar esse elemento e todos os filhos que estão abaixo dele?')) {
                return excluir(param1.id);
            } else {
                return false;
            }
        } else if ('copy_node' === nomeEvento) {
            colar({copiado: param1.id, colado: param2.id});
        } else if ('create_node' === nomeEvento) {
            salvar({nome: param1.text, idPai: param2.id, pos: param3});
        } else if ('rename_node' === nomeEvento) {
            salvar({nome: param3, uuid: param1.id, idPai: param2.id});
        }
    }

    function pegaListaFilhos(idPai)
    {
        var lista = [];
        var i = 0;

        $('#'+idPai).children('ul').children('li').each(function(){
            lista.push({'pos' : i, 'id' : $(this).attr('id')});
            ++i;
        });
        return {id : idPai, lista : lista};
    }

    function excluir(id)
    {
        var retorno = false;
        abreLoader('show');
        
        $.ajax({ // -- HASH_CONTENT_SERVICO_DELETA_TREE
            url: location.pathname+'?servico=f2b4b2db-888f-49be-84fd-41ab3dc2177f',
            dataType: 'json',
            data: {uuid: id},
            method: 'post',
            async: false,
            success: function(data){
                if (data.msg !== '') {
                    alert(data.msg);
                } else {
                    retorno = true;
                }
            },
            complete: function(){
                fechaLoader();
            }
        });
        
        return retorno;
    }

    function colar(params)
    {
        abreLoader('show');
        $.ajax({ // -- HASH_CONTENT_SERVICO_COPIA_TREE
            url: location.pathname+'?servico=c4b118cb-0ca6-4497-af42-12742aab4740',
            dataType: 'json',
            data: params,
            method: 'post',
            success: function(data){
                if (data.msg !== '') {
                    alert(data.msg);
                    fechaLoader();
                } else {
                    refreshTree();
                }
            },
            complete: function(){
                fechaLoader();
            }
        });
    }
    
    var incNovosNodes = 1;
    
    function salvar(params)
    {
        abreLoader('show');
        $.ajax({ // -- HASH_CONTENT_SERVICO_SALVA_TREE
            url: location.pathname+'?servico=bbc2737c-5833-11e6-8679-d768485170df',
            dataType: 'json',
            data: params,
            method: 'post',
            success: function(data){               
                if (data.msg !== '') {
                    alert(data.msg);
                }
                if (data['uuid'] !== '' && data['uuid'] !== undefined) {
                    var node = $('#jstree').jstree(true).get_node('#j1_'+incNovosNodes);
                    $('#jstree').jstree(true).set_id(node, data.uuid);
                    ++incNovosNodes;
                }
            },
            complete : function(data){
                fechaLoader();
            }
        });
    }
});

function abreLoader(type)
{
    if(type === 'show'){
        $('#loader').show();
    }else{
        $('#loader').fadeIn('slow');
    }
}

function fechaLoader()
{
    $('#loader').fadeOut('slow');
}

function trataForm(form)
{
    var formNovo = $(form).serializeArray();
    var nome = $('textarea').attr('id');
    formNovo[5]['value'] = CKEDITOR.instances[nome].getData();
    return formNovo;
}