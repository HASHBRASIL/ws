<?php

    require_once "connect.php";
    $id                = $_POST['id_grupo'];
    $grupos            = array();
    $gruposOrganizados = array();

    $query = $dbh->prepare(
        "WITH RECURSIVE tb_todos_grupos (id, nome, id_pai, metanome, publico) AS
        (
            SELECT id, nome, id_pai, metanome, publico from tb_grupo WHERE id_pai = ?
        UNION ALL
            SELECT tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai, tb_grupo.metanome, tb_grupo.publico FROM tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
        )
        SELECT id, nome, id_pai, metanome, publico,
            CASE WHEN EXISTS ( SELECT 1 FROM tb_grupo g1 WHERE g1.id_pai = tb_todos_grupos.id ) THEN 1
            ELSE 0
        END AS pai,
        CASE id_pai WHEN ? THEN 1
            ELSE 0
        END AS principal
        FROM tb_todos_grupos");

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
?>

<div class="panel panel-default">
    <div class="panel-heading">Novo Item de Menu</div>
    <div class="panel-body">

        <form action="includes/insertMenu.php" id="formMenu" method="POST">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome do Menu">
            </div>
            <div class="col-md-12">
                <label for="">Situação do Dado</label>
                <div class="radio">
                    <label>
                        <input type="radio" name="publico" id="optionsRadios1" value="t" checked>
                        Público
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="publico" id="optionsRadios2" value="f">
                        Não público
                    </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="">Selecione a TIB</label>
                    <select id='selectTipMaster' class="form-control">
                        <option value="" selected></option>
                        <?php
                            foreach($tibs as $key => $value){
                                echo "<option value='". $value['id_tib'] ."'>" . str_pad($value['count'], 4, '0', STR_PAD_LEFT) . " | " . $value['nome'] . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group" id="containerSelect"></div>
            </div>
            <div class="form-group col-md-12">
                <button type="submit" id="adicionarMenu" class="btn btn-default pull-right">Salvar</button>
                <button type="submit" id="editarMenu" class="btn btn-default pull-right">Salvar Edição</button>
            </div>
            <input type="hidden" value='' name="idConteudo" id="idConteudo"/>
            <input type="hidden" value='' name="idSelecionado" id="idSelecionado"/>
            <input type="hidden" value='' name='idMenuPai' id='idMenuPai' />
            <input type="hidden" value="<?php echo $id; ?>" name="idSite"/>
            <input type="hidden" value='' name='deselected' id='deselected' />
        </form>
        <div id="mensagens" class="alert alert-success hidden" role="alert" style="margin-top: 220px;"></div>
    </div>
</div>

<script>
    var arGruposOrganizado = <?php echo json_encode($gruposOrganizados); ?>;
    var arGrupos           = <?php echo json_encode($grupos); ?>;
    var id                 = <?php echo json_encode($id); ?>;
    var ol                 = $(document.createElement('ol')).attr('id', 'sortable');
    var containerMenu      = $("#listMenu");

    containerMenu.append(ol);

    $("#selectTipMaster").chosen();

    $.each(arGruposOrganizado, function(i, v){
        $.each(v, function(index, value){
            if(value['principal']){
                geraMenu(value, ol);
            }
        });
    });

    function geraMenu(value, olContainer){
        var li           = $(document.createElement('li')).addClass('list-group-item').attr('data-public', value['publico']);
        var span         = $(document.createElement('span')).addClass('pull-right');
        var buttonDelete = $('<button type="button" class="btn btn-xs btn-default btn-danger pull-right deletarItem"><span class="glyphicon glyphicon-trash"></span></button>');
        var buttonEdit   = $('<button type="button" class="btn btn-xs btn-default btn-info pull-right editarItem"><span class="glyphicon glyphicon-edit"></span></button>');
        var div          = $(document.createElement('div')).addClass('clearfix');

        li.attr('data-id', 'id_' + value['id']);
        li.attr('id', value['id']);

        li.append(div);
        div.html(value['nome']);
        span.append(buttonDelete);
        span.append(buttonEdit);

        if(!value['metanome']){
            li.addClass('sortable');
            div.append(span);
        }else{
            li.attr('data-locked', 'true');
        }

        olContainer.append(li);

        if(value['pai']){
            var olPai = $(document.createElement('ol'));
            li.append(olPai);

            $.each(arGruposOrganizado[value['id']], function(ind, val){
                geraMenu(val, olPai);
            });
        }
    }

    $("#selectTipMaster").change(function(e){
        id = $(this).children('option:selected').val();
        getTib(id);
    });

    function getTib(id){
        $.ajax({
            type: "POST",
            url: 'includes/ajaxGetib.php',
            data: {id: id},
            success: function(retorno){
                $("#selectTipMaster").chosen().find("option[value='"+ id +"']").prop('disabled', true);
                $("#selectTipMaster").trigger("chosen:updated");

                var rtn    = JSON.parse(retorno);
                var div    = $(document.createElement('div')).addClass('form-group');
                var select = $(document.createElement('select')).attr('multiple', 'multiple').addClass('form-control selectIb');
                var label  = $(document.createElement('label')).html( 'Seleciona a(o) ' +  $('#selectTipMaster > option[value="'+ id +'"]').text().split(' | ')[1]);

                $.each(rtn, function(ind, vle){
                    var opt = $(document.createElement('option'));
                    opt.html(vle[0]['conteudo']).attr('value', vle[0]['id_ib_pai']);
                    select.append(opt);

                });

                div.append(label);
                div.append(select);

                $('#containerSelect').append(div);
                select.chosen();
            }
        });
    }

    function getTibFulfilled(id, selected){

        $("#selectTipMaster").chosen().find("option").prop('disabled', false);
        $("#selectTipMaster").trigger("chosen:updated");

        $.ajax({
            type: "POST",
            url: 'includes/ajaxGetib.php',
            data: {id: id},
            success: function(retorno){

                var rtn    = JSON.parse(retorno);
                var div    = $(document.createElement('div')).addClass('form-group');
                var select = $(document.createElement('select')).attr('multiple', 'multiple').addClass('form-control selectIb');
                var label  = $(document.createElement('label')).html( 'Seleciona a(o) ' +  $('#selectTipMaster > option[value="'+ id +'"]').text().split(' | ')[1]);

                $.each(rtn, function(ind, vle){
                    $("#selectTipMaster").chosen().find("option[value='"+ id +"']").prop('disabled', true);
                    $("#selectTipMaster").trigger("chosen:updated");
                    var opt = $(document.createElement('option'));
                    opt.html(vle[0]['conteudo']).attr('value', vle[0]['id_ib_pai']);

                    $.map(selected, function(a){
                        if( a['id_item'] == vle[0]['id_ib_pai'] ){
                            opt.attr('selected', 'selected').attr('data-rl', a['id']);
                        }
                    });
                    select.append(opt);
                });

                div.append(label);
                div.append(select);

                $('#containerSelect').append(div);
                select.chosen().change(function(e, params){
                    if(params.selected){
                        var arPush = new Array();
                        arPush = $("#deselected").val().split(',');

                        if(arPush.indexOf(params.selected) != -1){
                            arPush.splice(arPush.indexOf(params.selected), 1);
                            $("#deselected").val(arPush.toString());
                        }
                    }

                    if(params.deselected){
                        var attr = $(this).find('option[value="' +params.deselected+ '"]').attr('data-rl');

                        if (typeof attr !== typeof undefined && attr !== false) {
                            var arPush = new Array();

                            if($("#deselected").val().length > 0 ){
                                arPush = $("#deselected").val().split(',');
                            }

                            if( arPush.indexOf( params.deselected ) == -1 ){
                                arPush.push(params.deselected);
                                $("#deselected").val(arPush.toString());
                            }
                        }
                    }
                });
            }
        });
    }

    $( "#sortable" ).nestedSortable({
        handle: "div",
        maxLevels:2,
        items: "li",
        toleranceElement: "> div",
        relocate: function(e, ui){
            if($(ui.item).attr('data-locked') && ($(ui.item).closest('ol').parent().attr('id') != containerMenu.attr('id')) ){
                $(this).sortable('cancel');
            }else{
                var dataPosition = $(this).nestedSortable('toHierarchy', {expression: '(.+)[_](.+)', attribute: 'data-id'});
                var id           = $(ui.item).attr('data-id').replace('id_', '');
                var valor        = $(ui.item).closest('ol').parent().attr('data-id').replace('id_', '');

                $.ajax({
                    url: 'includes/updateOrdemGrupo.php',
                    type: 'POST',
                    data: {valor: valor, id: id },
                    success: function(data){
                        console.log(data);
                        if( data == 'error' ){
                            mostraMSG( "#mensagems", 4, false );
                        }
                    }
                });
            }
        }
    }).disableSelection();

    //DELETAR MENU DO BANCO
    $(".deletarItem").click(function(){
        var pai  = $(this).parent().parent().parent();
        var idLi = pai.attr('id');

        if(confirm("DESEJA DELETAR ESSE ITEM!")){
            $.ajax({
                type: "POST",
                url: "includes/listMenuDelete.php",
                data: {rowid: idLi },
                success: function(data){
                    // console.log(data);

                    if( data == 'error' ){
                        mostraMSG( "#mensagems", 4, false );
                    }

                    pai.remove();
                }
            });
        }
    });

    //EDITAR MENU DO BANCO
    $("#editarMenu").hide();
    $('.editarItem').click(function() {
        var pai = $(this);
        var id  = pai.parent().parent().parent().attr('data-id').split('_')[1];

        $.ajax({
            url: 'includes/ajaxGetRLGrupoItem.php',
            type: "POST",
            data: {id_grupo: id},
            success: function(data){
                $("#nome").val(pai.parent().parent().text());
                $('#idSelecionado').val(pai.parent().parent().parent().attr('data-id').split('_')[1]);
                $("#editarMenu").show();
                $('#adicionarMenu').hide();
                $('#containerSelect').html('');

                if($('#sortable > li[id="'+ id +'"]').attr('data-public') == 'true' ){
                    $('input[name="publico"][id="optionsRadios1"]').prop('checked', true);
                }else{
                    $('input[name="publico"][id="optionsRadios2"]').prop('checked', true);
                }

                $.each($.parseJSON(data), function(index, value){
                    getTibFulfilled(index, value);
                });
            }
        });
    });

    $('#formMenu').submit(function(e) {
        var arConteudo = new Array();

        if($(this).find('button:focus').attr('id') == 'adicionarMenu'){

            $('.selectIb').each(function(i, v){
                arConteudo.push($(v).chosen().val());
            });
            $("#idConteudo").val(arConteudo.toString());

            $.ajax({
                url: 'includes/insertMenu.php',
                type: "POST",
                data: $(this).serialize(),
                success: function(data){

                    // console.log(data);
                    // console.log("\naaa");

                    if(data == 'error'){
                        $( "#mensagens" ).removeClass("hidden").removeClass("alert-success").addClass("alert-danger").show();
                        $( "#mensagens" ).text( "Ocorreu algum erro, entre em contato com o suporte." );

                        $( "#mensagens" ).fadeOut(3500, function(){
                            $("#selectEntity").trigger('change');
                        });

                    }else{
                        $( "#mensagens" ).removeClass("hidden").addClass("alert-success").css('margin-top', '280px').show();
                        $( "#mensagens" ).text( "Dados salvos com sucesso." );

                        $( "#mensagens" ).fadeOut(3500, function(){
                            $("#selectEntity").trigger('change');
                        });
                    }


                }
            });
        }

        if($(this).find('button:focus').attr('id') == 'editarMenu'){

            $('.selectIb').each(function(i, v){
                $.each( $(v).find('option:selected'), function(indi, valu){
                    arConteudo.push({
                        id_rl:   $(valu).attr('data-rl'),
                        id_item: $(valu).val()
                    });
                });
            });

            $.ajax({
                url: 'includes/updateGrupoMenu.php',
                type: "POST",
                data: {serialize: $(this).serialize(), conteudo: arConteudo},
                success: function(data){

                    if(data == 'error'){
                        $( "#mensagens" ).removeClass("hidden").removeClass("alert-success").addClass("alert-danger").show();
                        $( "#mensagens" ).text( "Ocorreu algum erro, entre em contato com o suporte." );

                        $( "#mensagens" ).fadeOut(3500, function(){
                            $("#selectEntity").trigger('change');
                        });
                    }else{
                        $( "#mensagens" ).removeClass("hidden").addClass("alert-success").show();
                        $( "#mensagens" ).text( "Dados atualizados com sucesso." );

                        $( "#mensagens" ).fadeOut(3500, function(){
                            $("#selectEntity").trigger('change');
                        });
                    }

                    // $("#selectEntity").trigger('change');
                }
            });
        }

        e.preventDefault();
    });
</script>
