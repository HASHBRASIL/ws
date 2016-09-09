<?php
//http://localhost/hashgit/home.php?servico=231a985c-13f5-4898-aa89-37644e4d13ec
require_once "connect.php";

$id                = $_POST['id_grupo'];
$grupos            = array();
$gruposOrganizados = array();

$query = $dbh->prepare(
    "WITH RECURSIVE tb_todos_grupos (id, nome, id_pai, metanome, publico) AS
    (
        SELECT id, nome, id_pai, metanome, publico from tb_grupo WHERE id = ?
        UNION
        SELECT tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai, tb_grupo.metanome, tb_grupo.publico FROM tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
        )
SELECT rec.id, rec.nome, rec.id_pai, rec.metanome, rec.publico, gm.valor as ordem,
CASE WHEN EXISTS ( SELECT 1 FROM tb_grupo g1 WHERE g1.id_pai = rec.id ) THEN 1
ELSE 0
END AS pai,
CASE id_pai WHEN ? THEN 1
ELSE 0
END AS principal
FROM tb_todos_grupos rec LEFT OUTER JOIN (select CAST(coalesce(valor, '-1') AS integer) as valor, id_grupo from tb_grupo_metadata where metanome = 'ws_ordem') gm on (rec.id = gm.id_grupo) ORDER BY ordem
");

$query->execute(array($id, $id));

$queryTib = $dbh->prepare(
    "SELECT
    mixtable.count, tib.nome, mixtable.id_tib
    FROM tp_itembiblioteca tib
    LEFT OUTER JOIN
    ( SELECT count(*), ib.id_tib FROM tb_itembiblioteca AS ib WHERE ib.id_tib IN ( SELECT id FROM tp_itembiblioteca WHERE tipo = 'Master' ) GROUP BY ib.id_tib ) AS mixtable
    ON ( mixtable.id_tib = tib.id )
    WHERE
    tib.id_tib_pai IS NULL AND mixtable.count > 0"
    );


$queryTib->execute(array());
$tibs    = $queryTib->fetchAll();
$grupos  = $query->fetchAll();

foreach($grupos as $key => $value){
    $gruposOrganizados[$value['id_pai']][] = $value;
}
 // echo "<pre>";
 // var_dump($gruposOrganizados);
 // die();

?>
<style>
.listbutton {
    display:none;
}
.container_titulo {
    border: 1px solid #888;
    text-align: center;
    border-left: 5px solid;
    display:block;
    border-left-color:blue;

}
.listbutton > li {
    background: #F3C4B0;
    list-style: none;
    display:block;
    margin-bottom: 7px;
    color: #9C6850;
}

.close {
    display:none;
}

.minus {
    display:none;
}

</style>
<div class="row" id="container_menu">
    <div class="col-md-3 menu_portal" >
        <div>
            <div class="container_titulo">
                <h1>Noticia</h1>
                <button class="add_menu">
                    <span class="glyphicon glyphicon-plus pull-right plus"></span>
                    <span class="glyphicon glyphicon-minus pull-right minus"></span>
                </button>
            </div>

            <ul class="listbutton">
                <li class="btn btn-default" title="Listar ultimas noticias" data-toggle="popover" data-trigger="hover" data-content="Lorem ipsum dolor sit amet, lacus tempor vitae sit. Nisl condimentum, augue urna lacinia pellentesque id."><strong>Listar ultimas noticias</strong></li>
                <li class="btn btn-default" title="Listar ultimas noticias" data-toggle="popover" data-trigger="hover" data-content="Lorem ipsum dolor sit amet, lacus tempor vitae sit. Nisl condimentum, augue urna lacinia pellentesque id."><strong>Listar Noticias Gabinete</strong></li>
                <li class="btn btn-default" title="Listar ultimas noticias" data-toggle="popover" data-trigger="hover" data-content="Lorem ipsum dolor sit amet, lacus tempor vitae sit. Nisl condimentum, augue urna lacinia pellentesque id."><strong>Minhas noticias</strong></li>
                <li class="btn btn-default" title="Listar ultimas noticias" data-toggle="popover" data-trigger="hover" data-content="Lorem ipsum dolor sit amet, lacus tempor vitae sit. Nisl condimentum, augue urna lacinia pellentesque id."><strong>Noticias por Partido</strong></li>
            </ul>
            <ol class="menus sortable">

            </ol>
        </div>
    </div>
</div>

<script>
var arGruposOrganizado = <?php echo json_encode($gruposOrganizados); ?>;
var arGrupos           = <?php echo json_encode($grupos); ?>;
var id                 = <?php echo json_encode($id); ?>;
var containerMenu      = $("#listMenu");
var id_site            = $('#selectEntity > option:selected').val();
var clone              = $('.menu_portal:first').clone();
$('.menu_portal:first').remove();

$.each(arGruposOrganizado[id_site], function(i, v){
    var novo_clone = clone.clone();
    $('#container_menu').append(novo_clone);
    novo_clone.find('.container_titulo').attr('data-id', 'id_' + v['id']);
    novo_clone.find('.container_titulo > h1').text(v['nome']);
    novo_clone.find('.listbutton > li').click(function(){

        var li           = $(document.createElement('li')).addClass('list-group-item').attr('data-public', v['publico']).attr('data-ordem', v['ordem']);
        var span         = $(document.createElement('span')).addClass('pull-right glyphicon glyphicon-remove-sign close');
        var div          = $(document.createElement('div')).addClass('clearfix');

        li.attr('data-id', 'id_' + v['id']);
        li.attr('id', v['id']);


        li.append(div);
        div.html($(this).text());
        div.append(span);

        $(this).parent().parent().find('.menus').append(li);

        li.parent().find('li').each(function(indice, valor){
        $(valor).attr('data-ordem', indice + 1);

         });

        li.hover(function(){
            $(this).find('div>.close').show();
        },function () {
            $(this).find('div>.close').hide();
        });

        li.find('div>.close').click(function(){
            var li = $(this).parent().parent();
            var ul = li.parent();
            li.remove();

            ul.find('li').each(function(indice, valor){
                $(valor).attr('data-ordem', indice + 1);

            });
        });

    });
    novo_clone.find('.add_menu').click(function(){
        $(this).parent().parent().find('.listbutton').show("slow");

    });

    $.each(arGruposOrganizado[v['id']],function(indice,valor){
        geraMenu(valor,novo_clone.find('.menus'))
    });

});


function geraMenu(value, olContainer){

    var li           = $(document.createElement('li')).addClass('list-group-item').attr('data-public', value['publico']).attr('data-ordem', value['ordem']);
    var span         = $(document.createElement('span')).addClass('pull-right glyphicon glyphicon-remove-sign close');
    var div          = $(document.createElement('div')).addClass('clearfix');

    li.attr('data-id', 'id_' + value['id']);
    li.attr('id', value['id']);


    li.append(div);
    div.html(value['nome']);
    div.append(span);


    li.hover(function(){
        $(this).find('div>.close').show();
    },function () {
        $(this).find('div>.close').hide();
    });

    li.find('div>.close').click(function(){
        var li = $(this).parent().parent();
        var ul = li.parent();
        li.remove();

        ul.find('li').each(function(indice, valor){
            $(valor).attr('data-ordem', indice + 1);

        });
    });


    olContainer.append(li);

    if(value['pai']){
        var olPai = $(document.createElement('ol')).addClass('sortable');
        li.append(olPai);

        $.each(arGruposOrganizado[value['id']], function(ind, val){
            geraMenu(val, olPai);
        });
    }
}

$('[data-toggle="popover"]').popover();

$( ".sortable" ).nestedSortable({
    handle: "div",
    maxLevels:1,
    items: "li",
    toleranceElement: "> div",
    relocate: function(e, ui){

        var id     = $(ui.item).attr('data-id').replace('id_', '');
        //var valor  = $(ui.item).closest('ol').parent().attr('data-id').replace('id_', '');


        arOrdenacao = new Array();
        $.each($(ui.item).parent().find('li'), function(indice, valor){
            arOrdenacao.push( {filhos: [], metanome: 'ws_ordem', valor: indice + 1, id_grupo: $(valor).attr('id')} );
            $(valor).attr('data-ordem', indice + 1);

        });

        // $.ajax({
        //  url: 'includes/updateOrdemGrupo.php',
        //  type: 'POST',
        //  data: {valor: valor, id: id, order: arOrdenacao },
        //  success: function(data){
        //      console.log(data)

        //  }
        // });

}


}).disableSelection();
</script>
