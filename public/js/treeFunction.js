var to = false;
        
$(document).ready(function() 
{
    function refreshTree()
    {
        $('#jstree').jstree(true).refresh();
        abreLoader();
    }

    $('body').on('click', '.grupoDivMetas button', function(e){
        
        e.preventDefault();
        var data = {'id_grupo' : $('#idGrupo').val(), 'metanome' : $(this).attr('metanome')};
        abreLoader();
        
        $.ajax({
            url     : location.pathname+'?servico=58adcb84-4abd-11e6-85b8-e3dbe645fe4f',
            data    : data,
            dataType : 'json',
            method  : 'post',
            success : function(data){
                fechaLoader();
                data.msg === '' ?  selecionaNode('', {event : {button : 3}, node : {id : $('#idGrupo').val()}}) : alert(data.msg);
            },
            error : function(){
                alert('erro');
                fechaLoader();
            }
        });
    });

    $('body').on('change', '.grupoDivPropriedades table input[type="text"]', function(e){
        e.preventDefault();
        var data = {'id_grupo' : $('#idGrupo').val(), 'coluna' : $(this).attr('coluna'), 'valor' : $(this).val()};
        abreLoader();
 
        var buttonEvent = data['coluna'] === 'nome' ? 0 : 2;

        $.ajax({
            url     : location.pathname+'?servico=8be3474a-4abd-11e6-85b8-3f8d2b5ce1c7',
            data    : data,
            dataType : 'json',
            method  : 'post',
            success : function(data){
                fechaLoader();
                data.msg === '' ?  selecionaNode('', {event : {button : buttonEvent}, node : {id : $('#idGrupo').val()}}) : alert(data.msg);
            },
            error : function(){
                alert('erro');
                fechaLoader();
            }
        });
    });
    
    $('body').on('submit', '.grupoDivMetas form', function(e){
        e.preventDefault();
        var data = $(this).serialize()+'&id_grupo='+$('#idGrupo').val();
        abreLoader();
        
        $.ajax({
            url     : location.pathname+'?servico=30dd104c-4abd-11e6-85b8-9bcc2cc17258',
            data    : data,
            dataType : 'json',
            method  : 'post',
            success : function(data){
                fechaLoader();
                data.msg === '' ?  selecionaNode('', {event : {button : 1}, node : {id : $('#idGrupo').val()}})  : alert(data.msg);
            },
            error : function(){
                alert('erro');
                fechaLoader();
            }
        });
    });
    
    $('body').on('change', '.grupoDivMetas table input[type="text"]', function(e){
        e.preventDefault();
        var data = {'id_grupo' : $('#idGrupo').val(), 'metanome' : $(this).attr('metanome'), 'valor' : $(this).val()};
        abreLoader();
        
        $.ajax({
            url     : location.pathname+'?servico=6b4365f6-4abd-11e6-85b8-53641b92c447',
            data    : data,
            dataType : 'json',
            method  : 'post',
            success : function(data){
                 fechaLoader();
                data.msg === '' ?  selecionaNode('', {event : {button : 1}, node : {id : $('#idGrupo').val()}}) : alert(data.msg);
            },
            error : function(){
                 fechaLoader();
                alert('erro');
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
                  'url' : function (node) {

                      return  location.pathname+
                              '?servico=64dd14de-4a89-11e6-a379-63fc7ace1e27&'+
                              $('.form-tree').serialize();
                  },
                  "callback": function(){alert(42)},
                  "data_callback": function(){alert(444)},
              }
        },
        
        "types" : {
            "#" : {"max_depth" : 5, "valid_children" : ["root"]},
            "root" : {"icon" : "glyphicon glyphicon-file", "valid_children" : ["detalhe", "root"]},
            "time" : {'icon' : "fa fa-users"},
            "site" : {'icon' : "fa fa-sitemap"},
            "mensageria-time" : {'icon' : "ico-mensageria-time"},
            "mensageria" : {'icon' : "fa fa-hashtag"},
            "default" : {'icon' : "fa fa-folder-o"},
        }, "dnd"  :   {
            "drop_target"   :   true,
            "drag_check"    :   function(data) { return true;  } 
        },
        "plugins" : [
            "dnd", "contextmenu", "search", "state", "types"/*, "json_data"*/
        ]
    })
//    .on('paste.jstree', function(e, data){    
//        //evento para colar
//        console.log(333)
//    })
    .on('select_node.jstree', selecionaNode)
    .on("move_node.jstree", function (e, data) {
        console.log(data)
        var objeto = {  
                        'uuid' : data.node.id,
                        'idPai' : data.node.parent,
                        'reordenarGrupos' : [
                            pegaListaFilhos(data.node.old_parent),
                            pegaListaFilhos(data.node.parent)
                        ]
                    };
        salvaGrupo(objeto);
    })
    .on("refresh.jstree", function () {
        fechaLoader();
    });


    function selecionaNode(e, data)
    {
        if(data !== undefined && data.event !== undefined && data.event.button !== 2){
            $('.editar-propriedade').html('<i class="fa fa-refresh fa-spin fa-3x fa-fw" aria-hidden="true"></i>');
            abreLoader();
            $.ajax({
                url : location.pathname+'?servico=bf1851fe-4a9b-11e6-a379-671d9d311490',
                data : {id_grupo : data.node.id},
                method : 'post',
                success : function(data){
                    $('.editar-propriedade').html(data);
                    fechaLoader();
                },
                error : function(){
                    $('.editar-propriedade').html('Erro...');
                    fechaLoader();
                }
            });
        }
    }

    function beforeCallback(nomeEvento, param1, param2, param3){

        if('delete_node' === nomeEvento){
            if(confirm('Deseja deletar esse elemento e todos os filhos que estão abaixo dele?')){
                return excluiGrupo(param1.id);
            }else{
                return false;
            }
        }else if('copy_node' === nomeEvento){
            colaGrupo({copiado : param1.id, colado : param2.id});
        }else if('create_node' === nomeEvento){
            salvaGrupo({nome : param1.text, idPai : param2.id, pos : param3});
        }else if('rename_node' === nomeEvento){
            salvaGrupo({nome : param3, uuid : param1.id, idPai : param2.id});
        }else if('move_node' === nomeEvento){
            console.log(arguments)
            return true;
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

    function excluiGrupo(id)
    {
        var retorno = false;
        
         abreLoader('show');
        
        $.ajax({
            url : location.pathname+'?servico=6870f6b0-486d-11e6-a2ed-d3f712ba68bd',
            dataType : 'json',
            data : {uuid : id},
            method : 'post',
            async: false,
            success : function(data){
                if(data.msg !== ''){ 
                    alert(data.msg);
                }else{
                    retorno  = true;
                }
            },
            complete : function(){
                fechaLoader();
            }
        });
        
        return retorno;
    }

    function colaGrupo(params)
    {
        abreLoader('show');
        $.ajax({
            url : location.pathname+'?servico=6c5502ca-4931-11e6-96dc-2b6e08c74161',
            dataType : 'json',
            data : params,
            method : 'post',
            success : function(data){
                if(data.msg !== ''){
                    alert(data.msg);
                    fechaLoader();
                }else{
                    refreshTree();
                }
            },
            complete : function(){
                fechaLoader();
            }
        });
    }
    
    var incNovosNodes = 1;
    
    function salvaGrupo(params)
    {
        abreLoader('show');
        
        $.ajax({
            url : location.pathname+'?servico=678d9270-4869-11e6-a2ed-bb23cd9a5ecd',
            dataType : 'json',
            data : params,
            method : 'post',
            success : function(data){
                if(data.msg !== ''){
                    alert(data.msg);
                }

                if(data['uuid'] !== '' && data['uuid'] !== undefined){
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