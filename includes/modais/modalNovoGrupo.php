<div class="modal fade" id="modalNovoGrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
                <div class="page-header">
                    <h1>Usuário: <?= $usuario->nome; ?></h1>
                    <span>Criar um novo Grupo</span>
                </div>
            </div>
            <!-- <form accept-charset="utf-8" action="<?= base_url()."includes/modais/insertModalNovoGrupo.php"; ?>" class="modalFormNovoGrupo">
                <div class="modal-body">
                    <div class="row wrapper wrapper-white">

                        <div class="col-md-12 content">
                            <div class="form-group-one-unit">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Nome do grupo <span>Exemplo de Nome</span></label>
                                            <input type="text" placeholder="Nome" id="" name="nome-modalNovoGrupo" class="form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Alias <span>nome único para o usuário</span></label>
                                            <input type="text" placeholder="Nome" id="" name="alias-modalNovoGrupo" class="form-control input-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Descrição: </label>
                                            <textarea class="form-control ckeditor" name="desc-modalNovoGrupo"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="idUsuario" value="<?= $sessionUserID; ?>">
                                <input type="hidden" name="idTime"    value="<?= $sessionTimeID; ?>">

                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="retornoNovoGrupo"></div>
                    </div>
                </div>
            </form> -->
            <?php
                $idServico   = getIdServico( 'emdt_creategrupo', $dbh, $param = PDO::FETCH_ASSOC )[0];
                $buscaMetada = $dbh->prepare( 'SELECT * FROM tb_servico_metadata WHERE id_servico = :id' );
            ?>
        </div>
    </div>
</div>

<script>
    // CKEDITOR.replace(
    //     'desc-modalNovoGrupo', {
    //        toolbar:[
    //          { name: 'basicstyles', items : [ '' ] }, { name: 'paragraph', items : [ '' ] }, { name: 'paragraph', items : [ ''] },
    //          { name: 'styles',      items : [ '' ] }, { name: 'colors',    items : [ '' ] }, { name: 'clipboard', items : [ '' ] },
    //          { name: 'tools',       items : [ '' ] }
    //        ], height: "200px"}
    // );

    // frm = $( '.modalFormNovoGrupo' );
    // frm.submit( function( e ){
    //     e.preventDefault();

    //     form = $( this ).serializeArray();
    //     getCKEditorData( $( this ), form );

    //     console.log( form );

    //     $.ajax( {
    //         url: $( this ).attr( 'action' ),
    //         type: 'POST',
    //         data: form,
    //         success: function( res ){
    //             console.log( res )
    //             $( '.retornoNovoGrupo' ).html( res );
    //         }
    //     });
    // })
</script>