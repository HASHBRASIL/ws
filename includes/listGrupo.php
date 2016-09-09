<?php
    require_once "connect.php";
    require_once "functions.php";
    $sessionUserID = $_SESSION['USUARIO']['ID'];
    $times         = getEntidadesByUser( $dbh, $sessionUserID );
    $grupos        = getGrupos( $dbh, null, PDO::FETCH_OBJ );

    $queryGruposHash = $dbh->query(
        "SELECT
            rgp.nomehash, grupo.id ,grupo.nome, grupo.id_criador, grupo.id_pai, grupo.id_representacao, grupo.descricao, grupoMD.valor AS arquivo
        FROM
            tb_grupo grupo
        FULL OUTER JOIN
            tb_grupo_metadata grupoMD ON ( grupo.id = grupoMD.id_grupo )
        JOIN
            rl_grupo_pessoa rgp       ON ( grupo.id = rgp.id_grupo     )
        WHERE
            rgp.nomehash IS NOT NULL
            AND
            grupo.publico = 't'"
    );
    $queryGruposHash->execute();
    $gruposHash = $queryGruposHash->fetchAll( PDO::FETCH_OBJ );
?>

<div class="modal fade" id="modalAtencao" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <!-- formulario -->
                <div class="row insertImg">
                    <div class="col-xs-6 col-md-3">
                        <a href="" class="thumbnail">
                            <img id="imgGrupo" src="" alt="">
                        </a>
                    </div>
                </div>

                <?php include_once "formModalGrupo.php"; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-xs btn-warning" data-action="atualizar">Atualizar</button>
                <button type="button" class="btn btn-xs btn-default" data-dismiss="modal"   >Fechar</button>
            </div>
            <div class="retorno"></div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <p class="text-center textoModal"><i class="fa fa-exclamation-circle fa-5x"></i> <br> <span>Deseja Apagar Item</span></p>
                    </div>
                    <div class="col-md-3"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-xs btn-warning" data-action="deletar">Sim</button>
                <button type="button" class="btn btn-xs btn-default" data-dismiss="modal" >Não</button>
            </div>
        </div>
    </div>
</div>

<div class="row-wrapper">
    <div class="page-header">
        <h1><?php echo $SERVICO['nome']; ?></h1>
        <span><?php echo $SERVICO['descricao']; ?></span>
    </div>
    <div class="col-md-12">


        <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
            <li class="active"><a href="#tabContentTodos" data-toggle="tab">Lista: Todos os Grupos</a></li>
            <li><a href="#tabContentGruposHash" data-toggle="tab">Lista: Grupos Hash</a></li>
        </ul>
        <div id="my-tab-content" class="tab-content">
            <div class="tab-pane active" id="tabContentTodos">
                <div class="table-responsive">
                    <table id="tableGrupo" class="table table-condensed table-striped">
                        <thead>
                            <th>id</th>
                            <th>dtype</th>
                            <th>dt_inclusao</th>
                            <th>metanome</th>
                            <th>nome</th>
                            <th>publico</th>
                            <th>id_canal</th>
                            <th>id_criador</th>
                            <th>id_pai</th>
                            <th>id_representacao</th>
                            <th>descricao</th>
                            <th>ações</th>
                        </thead>
                        <?php
                            foreach ( $grupos as $grupo ) {
                                $editar  = '<button class="btn btn-xs btn-warning editar" type="button" data-id='.$grupo->id.'><i class="fa fa-pencil-square-o"></i></button>';
                                $deletar = '<button class="btn btn-xs btn-danger deletar" type="button" data-id='.$grupo->id.'><i class="fa fa-trash-o"></i></button>';
                                echo "<tr>";
                                    echo "<td>$grupo->id</td>";
                                    echo "<td>$grupo->dtype</td>";
                                    echo "<td>$grupo->dt_inclusao</td>";
                                    echo "<td>$grupo->metanome</td>";
                                    echo "<td>$grupo->nome</td>";
                                    echo "<td>$grupo->publico</td>";
                                    echo "<td>$grupo->id_canal</td>";
                                    echo "<td>$grupo->id_criador</td>";
                                    echo "<td>$grupo->id_pai</td>";
                                    echo "<td>$grupo->id_representacao</td>";
                                    echo "<td>$grupo->descricao</td>";
                                    echo "<td class='btnAcoes'>$editar $deletar</td>";
                                echo "</tr>";
                            }
                        ?>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="tabContentGruposHash">
                <div class="table-responsive">
                    <table id="tableGrupo" class="table table-condensed table-striped">
                        <thead>
                            <th>nomehash</th>
                            <th>id</th>
                            <th>nome</th>
                            <th>id_criador</th>
                            <th>id_pai</th>
                            <th>id_representacao</th>
                            <th>descricao</th>
                            <th>arquivo</th>
                        </thead>
                        <?php
                            foreach ( $gruposHash as $gruposHash ) {
                                echo "<tr>";
                                    echo "<td>$gruposHash->nomehash</td>";
                                    echo "<td>$gruposHash->id</td>";
                                    echo "<td>$gruposHash->nome</td>";
                                    echo "<td>$gruposHash->id_criador</td>";
                                    echo "<td>$gruposHash->id_pai</td>";
                                    echo "<td>$gruposHash->id_representacao</td>";
                                    echo "<td>$gruposHash->descricao</td>";
                                    echo "<td>$gruposHash->arquivo</td>";
                                echo "</tr>";
                            }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $( "#lsvisibilidade" ).chosen( { width: '100%' } );
    $( "#lstGrupo"       ).chosen( { width: '100%' } );
    $( "#lsentidades"    ).chosen( { width: '100%' } );

    var editor = CKEDITOR.replace(
        'editor', {
           toolbar:[
             { name: 'basicstyles', items : [ '' ] }, { name: 'paragraph', items : [ '' ] }, { name: 'paragraph', items : [ ''] },
             { name: 'styles',      items : [ '' ] }, { name: 'colors',    items : [ '' ] }, { name: 'clipboard', items : [ '' ] },
             { name: 'tools',       items : [ '' ] }
           ], height: "200px"}
    );

    $( "#lsentidades" ).change(function(){
        // Como criar uma regra de inserção de option por vez.
        // A pergunta é, como zerar a lista do choosen antes de adicionar novos itens
        $.ajax({
            method: "POST",
            url: 'includes/ajaxGruposPorEntidade.php',
            data: { 'id_entidade': $(this).children('option:selected').val() }
        }).done(function(rtn){
            var grupos = $.parseJSON( rtn );
            $.each( grupos, function( indice, value ){
                var option = $( document.createElement('option') ).val( value['id'] ).text( value['nome'] );
                $( "#lstGrupo" ).append( option );
            });
            $( "#lstGrupo" ).trigger("chosen:updated");
        });
    });

    // Editar
    $( ".btnAcoes" ).on('click', 'button.editar', function(){
        id = $( this ).attr( 'data-id' );

        $.ajax({
            url: 'includes/ajaxGetgrupo.php',
            method: 'POST',
            data: { "id_grupo": id },
        }).done(function ( response ) {
            var grupo = response[0];
            $( '.modal-title' ).text( "Editar grupo: "+grupo.nome );
            $( '#modalAtencao' ).modal( 'show' );

            // Adiciona imagem
            $.ajax({
                method: "POST",
                url: 'includes/ajaxGetdadosgrupo.php',
                data: { idGrupo: id }
            }).done(function( rtn ){
                if( rtn == "error" ){
                    $( "a.thumbnail" ).addClass( 'hidden' );
                }else{
                    var grupo = $.parseJSON( rtn );
                    $( "a.thumbnail" ).removeClass( 'hidden' );
                    $( "#imgGrupo" ).attr( 'src', grupo.arquivo );
                }
            });

            // Atribui valores aos campos do formulario
            $( "#nome"             ).val( grupo.nome );
            $('input[name=idGrupo]').val( grupo.id );
            editor.setData( grupo.descricao );
        });

        var btnAtualizar = $("[data-action='atualizar']");
        $( btnAtualizar ).bind( 'click', function() {
            var formUpdate = $( "#updateGrupo" );
            var data       = formUpdate.serializeArray();
            $.each( formUpdate.find( "textarea" ), function (i, v) {
                ck = editor.getData();
                $.each( data, function ( indice, value ) {
                    if ( value['name'] == $(v).attr( 'name' ) ) {
                        data[indice]['value'] = ck;
                    }
                });
            });

            // faz update
            $.ajax({
                url: formUpdate.attr('action'),
                method: 'POST',
                data: data,
            }).done(function ( response ) {
                var btn = $( "[data-action='atualizar']" );
                btn.text( "Atualizando..." ).prop( "disabled", true ).removeClass( 'btn-warning' ).addClass( 'btn-default' );
                if( response != 'error' ){
                    btn.text( response ).addClass( 'btn-success' );
                    $( $( "[data-dismiss='modal']" ) ).bind( 'click', function(){
                        location.reload();
                    });
                }
            });
        });
    });



    // Delete
    $( ".btnAcoes" ).on('click', 'button.deletar', function(){
        id = $( this ).attr( 'data-id' );
        $( '#modalDelete' ).modal( 'show' );
        $( '.modal-title' ).text( "" );

        var btnDeletar = $("[data-action='deletar']");
        $( btnDeletar ).bind( 'click', function() {
            // faz delete
            $.ajax({
                url: 'includes/deleteGrupo.php',
                method: 'POST',
                data: { "id": id },
            }).done(function ( response ) {
                var btn       = $( "[data-action='deletar']" );
                var btnFechar = $( "[data-dismiss='modal']" );
                btn.text( "Apagando..." ).prop( "disabled", true ).removeClass( 'btn-warning' ).addClass( 'btn-default' );
                if( response != 'error' ){
                    btn.text( response ).addClass( 'btn-success' );
                    $( ".textoModal" ).find( 'span' ).text( "Deltado com sucesso" );
                    $( 'i.fa.fa-exclamation-circle.fa-5x' ).removeClass( 'fa-exclamation-circle' ).addClass( 'fa-check-square-o' );
                    btnFechar.text( 'Fechar' );
                    $( btnFechar ).bind( 'click', function(){
                        location.reload();
                    });
                }
            });
        });
    });
</script>
