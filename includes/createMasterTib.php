<?php

    require_once "connect.php";

    $inputType = array('text','textarea','date','list');

    $tipoMaster    = "Master";
    $getMasterTibs = $dbh->prepare( "SELECT * FROM tp_itembiblioteca WHERE tipo = :tipoMaster" );
    $listaServicos = $dbh->query( "SELECT * FROM tb_servico" );
    $servicos      = $listaServicos->fetchAll( PDO::FETCH_ASSOC );

    try {

        $getMasterTibs->bindParam( ':tipoMaster', $tipoMaster );
        $getMasterTibs->execute();
        $dataMasterTibs = $getMasterTibs->fetchAll( PDO::FETCH_ASSOC );

    } catch ( PDOException $e ) {

        var_dump( $e->getMessage() );

    }

?>

<div class="row wrapper">
    <div class="page-header">
        <h1><?php echo $nome; ?></h1>
        <span><?php echo $descricao; ?></span>
        <?php include "includes/rastro.php"; ?>
    </div>
    <div class="col-md-12 content">
        <div class="form-header">
            <h1>Dados Tib <span>Breve descriçao do formulário</span></h1>
        </div>

        <form action="includes/insertMasterTib.php" method="post" accept-charset="utf-8" id="formTib" class="">
            <div class="form-group-one-unit">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tibNome">Nome</label>
                            <input type="text" class="form-control" id="tibNome_m" name="tibMaster_nome" placeholder="Nome Tib Master">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tib_descricao">Descrição</label>
                            <input type="text" class="form-control" id="tibDescricao_m" name="tibMaster_descricao" placeholder="Descrição">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-header">
                <h1>Herança Tib <span>Breve descriçao do formulário</span></h1>
            </div>

            <div class="form-group">
                <label for="heranca">Herança</label>
                <select name="heranca_id" id="heranca" class="form-control chosen-select">
                    <option value="" selected disabled>Escolha uma herança</option>
                    <?php
                        foreach( $dataMasterTibs as $key => $value ){
                            echo "<option value='". $value['id'] ."'>" . $value['nome'] . "</option>";
                        }
                    ?>
                </select>
            </div>

            <div class="form-header">
                <h1>Campos Tib <span>Breve descriçao do formulário</span></h1>
            </div>
            <div id="dadosFilhos">
                <div class="form-group form-inline row" id="rowItem_0">
                    <input type="text" placeholder="Nome" name="_nome" class="form-control" value="" />

                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Descrição" name="_descricao" value="">

                        <div class="input-group-btn">
                            <input type="text" class="form-control" placeholder="Ordem da Lista" name="_lista" value="" />

                            <div class="btn btn-default btn-icon-change visible">
                                <i class="fa fa-eye"></i>
                            </div>
                            <div class="btn btn-default btn-icon-change moveUp">
                                <i class="fa fa-chevron-up"></i>
                            </div>
                            <div class="btn btn-default btn-icon-change moveDown">
                                <i class="fa fa-chevron-down"></i>
                            </div>
                            <div class="btn btn-default btn-icon-change remove">
                                <i class="fa fa-trash-o"></i>
                            </div>

                            <select name="_tipo" data-id="" class="form-control selectTipo">
                                <option value="default" selected disabled>Tipo Campo</option>
                                <?php foreach( $inputType as $item ){
                                    echo '<option value="'.$item.'">'. strToUpper($item) .'</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" data-id="" value="" name="_wslista" />
                    <input type="hidden" data-id="" value="" name="_wsordem" />
                    <input type="hidden" data-id="" value="" name="_wsvisivel" />
                    <input type="hidden" data-id="" value="" name="_visivel" />
                    <input type="hidden" data-id="" value="" name="_ordem" />
                    <input type="hidden" data-id="" value="" name="_new" />
                </div>
            </div>

            <div class="row">
                <button type="button" class="btn btn-info btn-sm" id="createField">Novo Campo</button>
                <button type="submit" id="submitInsertMasterTib" class="btn btn-success btn-sm pull-right">Salvar</button>
            </div>
        </form>
        <div id="mensagens" class="alert alert-success hidden" role="alert" style="margin-top: 120px"></div>
    </div>
</div>

<script>

    $("#listaServico").chosen();
    $("#listaFluxo").chosen();

    var cloneGambs = $("#dadosFilhos > div[id='rowItem_0']").clone();
    $('#dadosFilhos > div:first').remove();

    function fulFillHeranca(ar){
        $.each(ar, function(ind, val){
            var newObj = cloneGambs.clone();
            if($("#dadosFilhos > div:last").length > 0 )
                var lastId = $("#dadosFilhos > div:last").attr("id").split('_')[1];
            else
                var lastId = -1;
            newObj.attr('id', 'rowItem_' + (++lastId));

            // Zera valores
            newObj.attr('data-id', val['id']);
            newObj.find('input[name="_nome"]'     ).attr('name', val['id']+'_nome').val(val['nome']).attr('disabled', 'disabled');
            newObj.find('input[name="_descricao"]').attr('name', val['id']+'_descricao').val(val['descricao']).attr('disabled', 'disabled');
            newObj.find('input[name="_lista"]'    ).attr('name', val['id']+'_lista').val(val['lista']);

            newObj.find('input[name="_visivel"]'  ).attr('name', val['id']+'_visivel').val( val['visivel'] );
            newObj.find('input[name="_ordem"]'    ).attr('name', val['id']+'_ordem').val( lastId );
            newObj.find('input[name="_new"]'      ).attr('name', val['id']+'_new').val('false');

            newObj.find('input[name="_wsvisivel"]').attr('name', val['id']+'_wsvisivel').val( val['visivel'] );
            newObj.find('input[name="_wsordem"]'  ).attr('name', val['id']+'_wsordem').val( lastId );
            newObj.find('input[name="_wslista"]'  ).attr('name', val['id']+'_wslista').val('0');
            newObj.find('input[name="_wsnew"]'    ).attr('name', val['id']+'_wsnew').val('0');

            newObj.find('select[name="_tipo"]'    ).attr('name', val['id']+'_tipo').attr('disabled', 'disabled').find('option[value="'+val['tipo']+'"]').attr('selected', 'selected');
            newObj.find('div.input-group-btn > div.remove').attr('disabled', 'disabled');

            if(val['visivel'] == null){
                newObj.find('div.input-group-btn > div.visible > i').removeClass('fa-eye').addClass('fa-eye-slash');
            }

            $("#dadosFilhos").append(newObj);
            $("#dadosFilhos >div:last").find('input[name="_ordem"]').val(lastId);
            $("#dadosFilhos >div:last").find('.btn-icon-change').click(btnForms);
        });
    }

    $("#heranca").change(function(){
        $.ajax({
            url: 'includes/ajaxGetTibChildren.php',
            type: 'POST',
            data: {id_tib_pai: $(this).val()},
            success: function(result){
                var data = JSON.parse(result);
                $('#dadosFilhos > div').remove();
                fulFillHeranca(data);
            }
        });
    }).chosen();

    $("#formTib").submit(function(e){
        e.preventDefault();
        var arGeral = [];
        var arMaster = {
            tibnome:   $("#tibNome_m").val(),
            descricao: $("#tibDescricao_m").val()
        };

        var arSetvico = {
            nomeServico:      $('#nome_servico').val(),
            descricaoServico: $('#descricao_servico').val(),
            selectServico:    $('#listaServico').val(),
            selectFluxo:      $('#listaFluxo').val()
        }

        $("#dadosFilhos > div[id*='rowItem_']").each(function(index, value){
            var nome       = $(value).find('input[name="_nome"]').val();
            var descricao  = $(value).find('input[name="_descricao"]').val();
            var visivel    = $(value).find('input[name="_visivel"]').val();
            var ordem      = $(value).find('input[name="_ordem"]').val();
            var ordemLista = $(value).find('input[name="_lista"]').val();
            var tipo       = $(value).find('.selectTipo > option:selected').val();

            arGeral.push({'nome': nome, 'descricao': descricao, 'visivel':visivel, 'ordem': ordem, 'tipo': tipo, 'ordemLista': ordemLista });
        });

        var dataTib = $("#formTib").serializeArray();

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: dataTib,
            success: function( response ) {
                // console.log(response);
                if( response == 'error' ){
                    // console.log('Erro ao salvar!');
                    // Mostra a mensagem com os dados retornados
                    mostraMSG( '#mensagens', 4, false );
                }else{
                    // console.log( response );
                    // Mostra a mensagem com os dados retornados
                    mostraMSG( '#mensagens', 1, true );
                }
            }
        });
    });

    function createClone(uuid){
        var newObj = cloneGambs.clone();
        if($("#dadosFilhos >div:last").length > 0)
            var lastId = $("#dadosFilhos >div:last").attr("id").split('_')[1];
        else
            var lastId = -1;

        newObj.attr('id', 'rowItem_' + (++lastId));

        // Zera valores
        newObj.attr('data-id', uuid);
        newObj.find('input[name="_nome"]'     ).attr('name', uuid+'_nome').val('');
        newObj.find('input[name="_descricao"]').attr('name', uuid+'_descricao').val('');
        newObj.find('input[name="_lista"]'    ).attr('name', uuid+'_lista').val('');

        newObj.find('input[name="_visivel"]'  ).attr('name', uuid+'_visivel').val('');
        newObj.find('input[name="_ordem"]'    ).attr('name', uuid+'_ordem').val( lastId );
        newObj.find('input[name="_new"]'      ).attr('name', uuid+'_new').val('true');

        newObj.find('input[name="_wsvisivel"]').attr('name', uuid+'_wsvisivel').val('');
        newObj.find('input[name="_wsordem"]'  ).attr('name', uuid+'_wsordem').val('');
        newObj.find('input[name="_wslista"]'  ).attr('name', uuid+'_wslista').val('');

        newObj.find('select[name="_tipo"]'    ).attr('name', uuid+'_tipo').removeAttr('disabled').find('option[value="default"]').attr('selected', 'selected');

        if(newObj.find('div.input-group-btn > div.btn-icon-change > i.fa-eye-slash').length)
            newObj.find('div.input-group-btn > div.btn-icon-change > i.fa-eye-slash').removeClass('fa-eye-slash').addClass('fa-eye');

        $("#dadosFilhos").append(newObj);
        $("#dadosFilhos >div:last").find('input[name="_ordem"]').val(lastId);
        $("#dadosFilhos >div:last").find('.btn-icon-change').click(btnForms);
    }

    $("#createField").bind('click', function(e){
        $.ajax({
            url: 'includes/geraUuid.php',
            type: 'POST'
        }).done(function(response) {
            createClone(response);
        });
    });

    //INICIA O PROCESSO DE DIVS COM IDS DINAMICOS ( hehe EU Não vou lembrar o que isso quer dizer B| )
    $('.btn-icon-change').click(btnForms);

    function btnForms(e){

        if($(this).find('i').hasClass('fa-eye')){
            var idRow = $(this).parent().parent().parent().attr("id").split("_")[1];
            $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
            $(this).parent().parent().parent().find( 'input[name="_visivel"]' ).val('f');
        }else if($(this).find('i').hasClass('fa-eye-slash')){
            var idRow = $(this).parent().parent().parent().attr("id").split("_")[1];
            $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            $(this).parent().parent().parent().find( 'input[name="_visivel"]' ).val('t');
        }

        if($(this).hasClass('moveUp')){
            moveObjs('moveUp', $(this));
        }else if($(this).hasClass('moveDown')){

            moveObjs('moveDown', $(this));
        }

        if($(this).hasClass('remove')){

            var attr = $(this).attr('disabled');

            if( !(typeof attr !== typeof undefined && attr !== false) ){
                if($(this).parent().parent().parent().parent().find('div[id*="rowItem_"]').length > 1 ){
                    $(this).parent().parent().parent().remove();
                    $('[id*="rowItem_"]').each(function(i, v){
                        $(v).attr('id', 'rowItem_' + i);
                    })
                }
            }
        }
    };

    function moveObjs(direction, objeto){
        var obj = objeto.parent().parent().parent();

        if(direction == "moveUp")
            var objPrev = obj.prev();
        else
            var objPrev = obj.next();

        if(objPrev.length ){
            var id = obj.attr('id');
            obj.attr('id', objPrev.attr('id'));
            objPrev.attr('id', id);
            obj.find('input[name*="_ordem"]').val(obj.attr('id').split("_")[1]);
            objPrev.find('input[name*="_ordem"]').val(objPrev.attr('id').split("_")[1]);
            if(direction == 'moveUp')
                obj.insertBefore(objPrev);
            else
                obj.insertAfter(objPrev);
        }
    }

</script>
