<div class="modal fade" id="modalNovoTime" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
                <div class="page-header">
                    <h1>Usuário: <?php echo $usuario->nome; ?></h1>
                    <span>Criar um novo time para o usuário.</span>
                </div>
            </div>
            <form accept-charset="utf-8" action="<?= base_url()."includes/modais/insertModalNovoTime.php"; ?>" class="modalFormNovoTime">
                <div class="modal-body">
                    <div class="row wrapper wrapper-white">

                        <div class="col-md-12 content">
                            <div class="form-group-one-unit">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Nome do time <span>Exemplo de Nome</span></label>
                                            <input type="text" placeholder="Nome" id="" name="nome-modalNovoTime" class="form-control input-sm">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Alias <span>nome único para o usuário</span></label>
                                            <input type="text" placeholder="Nome" id="" name="alias-modalNovoTime" class="form-control input-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Descrição:</label>
                                            <textarea class="form-control ckeditor" name="desc-modalNovoTime"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="idUsuario" value="<?php echo $sessionUserID; ?>">
                                <input type="hidden" name="idTime"    value="<?php echo $sessionTimeID; ?>">

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
                        <div class="retornoNovoTime"></div>
                    </div>
                </div>
            </form> -->
        </div>
    </div>
</div>

<script>
    CKEDITOR.replace(
        'desc-modalNovoTime', {
           toolbar:[
             { name: 'basicstyles', items : [ '' ] }, { name: 'paragraph', items : [ '' ] }, { name: 'paragraph', items : [ ''] },
             { name: 'styles',      items : [ '' ] }, { name: 'colors',    items : [ '' ] }, { name: 'clipboard', items : [ '' ] },
             { name: 'tools',       items : [ '' ] }
           ], height: "200px"}
    );

    frm = $( '.modalFormNovoTime' );
    frm.submit( function( e ){
        e.preventDefault();
        // console.log( $( this ) )
        form = $( this ).serializeArray();
        getCKEditorData( $( this ), form );

        console.log( form );
        // return false;

        $.ajax( {
            url: $( this ).attr( 'action' ),
            type: 'POST',
            data: form,
            success: function( res ){
                console.log( res )
                $( '.retornoNovoTime' ).html( res );
            }
        });
    })
</script>
