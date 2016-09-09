<?php
    require_once 'connect.php';
    require_once 'functions.php';
    $SIDU       = $_SESSION['USUARIO']['ID'];
    $entidades  = getEntidadesByUser( $dbh, $SIDU );
    $grupos     = getGrupos( $dbh, null, PDO::FETCH_ASSOC );
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
        <h1><?php echo $nome; ?></h1>
        <span><?php echo $descricao; ?></span>
        <?php include 'includes/rastro.php'; ?>
    </div>
    <div class="col-md-12 content">
        <div class="form-header">
            <h1>Escolha um Grupo <span>grupos</span></h1>
        </div>
        <div class="form-group">
            <label for="">Grupos</label>
            <select name="lstGrupos" id="lstGrupos" class="chosen-select">
                <option value="null" selected="" disabled="disabled">Escolha um Grupo</option>
                <?php foreach ( $grupos as $grupo ): ?>
                    <option value="<?php echo $grupo['id']; ?>" data-desc="<?php echo $grupo['descricao']; ?>"><?php echo $grupo['nome']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
<div class="row wrapper wrapper-white hidden" id="formDeEdicao">
    <div class="page-header">
        <h1 id="nomeGrupo"></h1>
        <span id="descricaoGrupo"></span>
    </div>
    <div class="col-md-12 content">
        <form action="includes/updateGrupo.php" method="post" accept-charset="utf-8" id="formGrupoUpdate" class="" enctype="multipart/form-data">

            <div class="row">

                <div class="col-md-3">
                    <div class="row insertImg">
                        <a href="" class="thumbnail hidden">
                            <img id="imgGrupo" src="" alt="">
                        </a>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="form-header" style="margin-left:20px">
                        <h1>Editar Grupo</h1>
                    </div>

                    <div class="col-md-12 content">
                        <div class="form-group-one-unit">
                            <!-- <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Entidade</label>
                                        <select name="lsentidades" id="lsentidades" class="form-control chosen-select">
                                            <option value="null" disabled="disabled">Escolha uma Entidade</option>
                                            <?php foreach( $entidades as $entidade): ?>
                                                <option value="<?php echo $entidade['id']; ?>"><?php echo $entidade['nome']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" value="" name="hiddenEntidade">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Escolha um Grupo Pai</label>
                                        <select name="lstgrupos" id="lstGrupo" class="form-control chosen-select">
                                            <option value="null">Escolha um Grupo</option>
                                        </select>
                                        <input type="hidden" value="" name="hiddenGrupoPai">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Visibilidade</label>
                                        <select name="lsvisibilidade" id="lsvisibilidade" class="form-control chosen-select">
                                            <option value="null" disabled="disabled">Escolha um tipo de visibildiade</option>
                                            <option value="t" selected="selected">Público</option>
                                            <option value="f">Privado</option>
                                        </select>
                                    </div>
                                </div>
                            </div> -->
                            <input type="hidden"  >
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nome">Nome</label>
                                        <input type="text" class="form-control input-sm" id="nome" name="nome" placeholder="Nome do Grupo">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="alias">Alias</label>
                                        <input type="text" class="form-control input-sm" id="alias" name="alias" placeholder="Alias do Grupo">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="descricao_grupo">Descrição</label>
                                        <textarea class="form-control" id="editor" name="descricao_grupo" placeholder="Descrição do Grupo" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="userID" value="<?php echo $SIDU; ?>">
                        </div>
                    </div>

                    <div class="col-md-12 content">
                        <div class="form-group-one-unit">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="nome_servico">Convite</label>
                                        <input type="text" class="form-control input-sm" id="" name="" placeholder="Ver lógica com Antônio">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <input type="submit" class="btn btn-info" value="salvar">
            <input type="hidden" id="idGrupo" name="idGrupo">
        </form>
    </div>
</div>

<script>
    var editor = CKEDITOR.replace(
        'editor', {
           toolbar:[
             { name: 'basicstyles', items : [ '' ] }, { name: 'paragraph', items : [ '' ] }, { name: 'paragraph', items : [ ''] },
             { name: 'styles',      items : [ '' ] }, { name: 'colors',    items : [ '' ] }, { name: 'clipboard', items : [ '' ] },
             { name: 'tools',       items : [ '' ] }
           ], height: "200px"}
    );

    $( '#lstGrupos' ).change(function () {
        $( "a.thumbnail" ).addClass( 'hidden' );
        $( "#formDeEdicao" ).removeClass( 'hidden' );
        $.ajax({
            url: 'includes/ajaxGetdadosgrupo.php',
            type: 'POST',
            data: { "idGrupo": $(this).children('option:selected').val() },
            success: function( result ){
                if( result == "error" ){
                    console.log( 'erro: acorreu algum erro, por favor entre em contato com o suporte' );
                }else{
                    var grupo = $.parseJSON( result );
                    // console.log( grupo );
                    if( grupo.arquivo ){
                        $( "a.thumbnail" ).removeClass( 'hidden' );
                        $( "#imgGrupo"   ).attr( 'src', grupo.arquivo );
                    }
                    $( "#nome"    ).val( grupo.nome  );
                    $( "#alias"   ).val( grupo.alias );
                    $( "#idGrupo" ).val( grupo.id );
                    editor.setData( grupo.descricao );
                }
            }
        });
        var desc = $(this).find('option:selected').attr('data-desc');
        $("#nomeGrupo").text('Grupo - ' + $(this).find('option:selected').text().split(' | ')[0] );
        $("#descricaoGrupo").html('Descrição - ' + ( desc == '' ? "Grupo não tem descrição" : desc ) );
    });

    // faz update
    $( "#formGrupoUpdate" ).submit( function( e ) {
        e.preventDefault();
        var data    = $( this ).serializeArray();

        // Percorre todos os textareas do form
        $.each( $( this ).find( "textarea" ), function ( i, v ) {
            editor = CKEDITOR.instances[$( v ).attr( 'id' )].getData();
            $.each( data, function ( indice, value ) {
                if ( value['name'] == $( v ).attr( 'name' ) ) {
                    data[indice]['value'] = editor;
                }
            });
        });

        // Faz update
        $.ajax({
            url: $( this ).attr( 'action' ),
            type: 'POST',
            data: data,
        }).done( function( rtn ){
            if( rtn == 'error' ){
                console.log( "Erro ao atualizar dados, por favor entre em contato com o suporte." );
                // console.log( rtn );
            }else{
                console.log( rtn );
            }
        });
    });


</script>
