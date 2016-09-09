<?php
    require_once 'connect.php';
    require_once 'functions.php';

    $queryCount = $dbh->prepare(
        "SELECT count(ib.id) AS qtd, tib.nome, tib.id, tib.descricao FROM
        tb_itembiblioteca ib RIGHT OUTER JOIN
        ( SELECT id,nome,descricao FROM tp_itembiblioteca WHERE tipo = 'Master') tib ON (ib.id_tib = tib.id)
        GROUP BY
            tib.nome, tib.id, tib.descricao
        ORDER BY
            qtd DESC"
    );

    // COLOCAR O HAVING DEPOIS DO GROUP BY
    // HAVING
    // count(ib.id) > 0

    $queryCount->execute();
    $countResult = $queryCount->fetchAll();

    if (isset($_GET['master'])){
        $idMaster = $_GET['master'];
    } else if (isset($SERVICO['id_tib'])){
        $idMaster = $SERVICO['id_tib'];
    } else {
        $idMaster = -1;
    }

    $fluxo = ( empty( $_GET['fluxo'] ) ) ? -1 : $_GET['fluxo'];
    $createDataMaster      = getIdServico( 'emdt_createdatamaster', $dbh );
    $servicoEditDataMaster = getIdServico( 'emdt_editdatamaster',   $dbh );
?>
<div class="modal fade modalAdvertencia" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title" style="font-size: 15px;">
                    Deseja deletar este item?
                </h2>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Não</button>
                <button type="button" class="btn btn-warning" id="btnModalDelete"><i class="fa fa-exclamation-triangle"></i> Sim</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modalSucesso" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div style="text-align: center;">
                    <i class="fa fa-check fa-4x text-success"></i>
                    <br>
                    Item removido com sucesso!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal" id="btnFecharModal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="row wrapper">
    <div class="page-header">
        <h1><?php echo $SERVICO['nome']; ?></h1>
        <span><?php echo $SERVICO['descricao']; ?></span>
    </div>
    <?php if( $idMaster == -1 ): ?>
    <div class="col-md-12 content">
        <div class="form-header">
            <h1>Escolha uma Tib <span>Tipo de item a ser listado abaixo</span></h1>
        </div>
        <div class="form-group">
            <label for="">Tibs</label>
            <select name="masters" id="masters" class="chosen-select">
                <option value=""></option>
                <?php foreach ($countResult as $item): ?>
                    <option value="<?php echo $item['id']; ?>" data-desc="<?php echo $item['descricao']; ?>"><?php echo $item['nome'] . " | " . $item['qtd']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="row">
            <a href="home.php?servico=<?php echo $createDataMaster; ?>" class="btn btn-success btn-sm pull-right" style="display: none;" id="novoItem">Novo</a>
        </div>
    </div>
    <?php endif; ?>
</div>
<div class="row wrapper wrapper-white">
    <div class="page-header">
        <h1 id="nomeTib">Lista - </h1>
        <span id="descricaoTib">Descrição - </span>
    </div>
    <div class="col-md-12 content">
        <div id="list_master">
            <table class='table table-hover' id='tableListMaster'>
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    //$('#tableListMaster').DataTable();
    fluxo                  = '<?php echo $fluxo ?>';
    metanomeServico        = JSON.parse( '<?php echo json_encode($createDataMaster); ?>' );
    metanomeEditDataMaster = JSON.parse( '<?php echo json_encode($servicoEditDataMaster); ?>' );
    idMaste                = <?php echo json_encode( $idMaster ) ?>;
    if( idMaste != -1 ){
        callAjaxListMaster( 0, idMaste );
    }

    function callAjaxListMaster(pag, id_master) {
        $.ajax({
            url: 'includes/ajaxlistMaster.php',
            type: 'POST',
            data: {pag: pag, id_master: id_master},
            success: function (retorno) {
                // console.log( retorno )
                var arRetorno = JSON.parse(retorno);

                thead = $('#tableListMaster > thead');
                tbody = $('#tableListMaster > tbody');

                if ( pag == 0 ) {
                    //CRIANDO O HEADER
                    $.each(arRetorno, function (indice, value) {
                        tr = $( '<tr>' );
                        $.each(value, function (ind, val) {
                            var th = $( '<th>' );
                            th.text(val['nome']);
                            tr.append(th);
                        });
                        tr.append( $( '<th>', { text: 'Ações' } ) );
                        thead.append(tr);

                        return false;
                    });
                }

                //PREENCHENDO A TABELA
                tamanhoArray = 0;
                $.each(arRetorno, function (indice, value) {
                    tr = $( '<tr>' );

                    tr.attr('data-id', indice);
                    tr.attr('data-master', id_master);

                    if(value.length > tamanhoArray){
                        tamanhoArray = value.length;
                    }

                    for( i=0; i < tamanhoArray; i++ ){
                        var td = $( '<td>' );
                        var a  = $( '<a>' );

                        if( value[i] != undefined ){
                            a.text(value[i]['valor']).attr( { 'class':'testeX', 'data-pk': value[i]['id'], 'data-url':'includes/updateFormInline.php', 'data-type': 'text', 'data-title': 'Edição in-line básica'  } );
                            // .attr( { 'data-pk': value[i]['id'], 'data-url': 'includes/updateFormInline.php'  } )
                            td.append( a );
                        }

                        tr.append(td);
                    }
                    // COLOCAR AQUI AS ACTIONS
                    // Cria botões
                    var btnDel = $('<button>', { class: 'btn btn-xs btn-default btn-danger pull-right deletarItem'+indice } );
                    var icoDel = $('<i>',   { class: 'glyphicon glyphicon-trash'} );
                    btnDel.append( icoDel );

                    btnDel.click(function(){
                         $( '.modalAdvertencia' ).modal( 'show' ).attr( 'data-id', indice ).attr( 'data-masterId', id_master );
                    });

                    var btnEdit = $('<button>', { class: 'btn btn-xs btn-default btn-info pull-right editaItem' } );
                    var icoEdit = $('<i>',   { class: 'glyphicon glyphicon-edit'} );
                    btnEdit.append( icoEdit );

                    // Cria botão de teste
                    var btnTeste = $( '<button>', { class: 'btn btn-xs btn-warning pull-right botaoTeste' } );
                    var icoTeste = $( '<i>', { class: 'glyphicon glyphicon-th-list' } );
                    btnTeste.append( icoTeste );

                    tr.append( $( document.createElement( 'td' ) ).append( btnDel ).append( btnEdit ).append( btnTeste ) );
                    tbody.append(tr);



                    // Adicionar ação ao botão de teste
                    var btnTeste  = $('#tableListMaster > tbody > tr > td > button.botaoTeste' );

                    btnTeste.unbind('click').bind('click', function (e) {
                        var trPai = $( this ).parent().parent();

                        if( trPai.next().attr( 'data-form' ) != "formEdit" ){

                            var itemTD   = $(this).parent().parent().attr('data-id');
                            var masterTD = $(this).parent().parent().attr('data-master')

                            var novaTR = $( '<tr>' );
                            novaTR.attr( 'data-form', 'formEdit' );

                            var vlrColspan = tamanhoArray + 1;
                            var novaTD = $( '<td colspan="'+ vlrColspan +'">' );
                            novaTR.append( novaTD );

                            trPai.after( novaTR );

                            $.ajax({
                                url: 'includes/ajaxListMasterData.php',
                                type: 'POST',
                                data: { id: itemTD, id_master: masterTD },
                                success: function ( response ){

                                    if( response == 'error' ){
                                        console.log( response );
                                    }else{
                                        // console.log( response );
                                        // retorno = JSON.parse( response );
                                        // console.log( retorno );
                                        novaTD.html( response );
                                    }

                                }
                            });
                        }

                    }).css('cursor', 'pointer');

                });
                // Adiciona campos de edição in-line básicos
                /*$('.testeX').editable({
                    validate: function( value ) {
                        if($.trim(value) == '')
                            console.log("a");
                        else if($.trim(value).length>30)
                            console.log("b");
                    }
                });*/

                var btnEditar  = $('#tableListMaster > tbody > tr > td > button.editaItem' );
                btnEditar.unbind('click').bind('click', function (e) {
                    if( fluxo == -1 ){
                        location.href = 'home.php?servico='+metanomeEditDataMaster.id+'&id=' + $(this).parent().parent().attr('data-id') + '&idMaster=' + $(this).parent().parent().attr('data-master')
                    }else{
                        location.href = 'home.php?servico='+metanomeEditDataMaster.id+'&id=' + $(this).parent().parent().attr('data-id') + '&idMaster=' + $(this).parent().parent().attr('data-master') + '&fluxo=' + fluxo
                    }
                }).css('cursor', 'pointer');
            }
        });
    }

    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            totalItens = $('#list_master > table > tbody > tr').length;
            callAjaxListMaster(totalItens, $("#masters").children('option:selected').val());
        }
    });

    $( '#masters' ).change(function () {
        $( "#novoItem" ).show();
        $( "#tableListMaster > tbody" ).html( '' );
        $( "#tableListMaster > thead" ).html( '' );
        callAjaxListMaster( 0, $( this ).children( 'option:selected' ).val() );

        $( '#novoItem' ).attr( 'href', 'home.php?servico='+metanomeServico.id+'&master=' + $( this ).children( 'option:selected' ).val() + '&fluxo=criar' );
        $("#nomeTib").text('Lista - ' + $(this).find('option:selected').text().split(' | ')[0] );
        $("#descricaoTib").text('Descrição - ' + $(this).find('option:selected').attr('data-desc') );

    });

    // Deletando item
    $( "#btnModalDelete" ).bind( 'click', function(){

        $( '.modalAdvertencia' ).modal( 'hide' );

        id       = $( '.modalAdvertencia' ).attr( 'data-id' );
        idMaster = $( '.modalAdvertencia' ).attr( 'data-masterId' );

        $.ajax({
            url: 'includes/deleteDataMaster.php',
            type: 'POST',
            data: { id: id, id_master: idMaster },
            success: function ( response ){

                if( response == 'error' ){
                    //console.log( response );
                }else{
                    //console.log( response );
                    $( '.modalSucesso' ).modal( 'show' );
                    $( '#btnFecharModal' ).bind( 'click', function(){
                        location.reload();
                    });
                }

            }
        });
    });
</script>
