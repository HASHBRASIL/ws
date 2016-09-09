<?php
    require_once "connect.php";
    require_once "UUID.php";
    require_once "functions.php";

    $idServico = $_GET['servico'];

    $query = $dbh->prepare( "SELECT * FROM tb_servico_metadata WHERE id_servico = :id AND metanome = 'ws_descricao'" );
    $query->bindParam( ':id', $idServico );
    $query->execute();
    $servicosMetadado = $query->fetchAll( PDO::FETCH_ASSOC );

    if( empty( $servicosMetadado ) ){
        // faz insert
        $action = "insert";
        $valor  = -1;
    }

    if( isset( $servicosMetadado[0]['valor'] ) ){
        // faz update
        $action = "update";
        $valor  = ( json_encode( $servicosMetadado[0]['valor'] ) == '' ) ? -1 : json_encode( $servicosMetadado[0]['valor'] );
    }
?>

<div id="mensagens" class="alert alert-success hidden" role="alert" style="margin-top: 60px"></div>

<form action="includes/insertNoTela.php" method="post" accept-charset="utf-8" id="form_<?= $SERVICO['id']; ?>" class="">

    <div class="row wrapper">
        <?php if(isset($SERVICO['metadata']['ws_comportamento']) && $SERVICO['metadata']['ws_comportamento'] == 'tab'): ?>
            <div class="page-header">
                <h1><?php echo $SERVICO['descricao']; ?></h1>
            </div>
        <?php else: ?>
            <div class="page-header">
                <h1><?php echo $SERVICO['nome']; ?></h1>
                <span><?php echo $SERVICO['descricao']; ?></span>
            </div>
        <?php endif; ?>
        <div class="col-md-12 content">
            <div class="form-group-one-unit">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nome_servico">Informações para está página</label>
                            <textarea class="form-control ckeditor" name="dadosEditor" id="editor" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="id_servico" value="<?php echo $idServico; ?>" >
                    <?php echo "<input type='hidden' name='action' value='$action'>"; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <button type="submit" class="btn btn-success .enviar">Salvar</button>
        </div>
    </div>
</form>

<script>
    var editor = CKEDITOR.replace( 'editor', {
        // Personalizar barra de tarefas do CKEditor
        toolbar :
        [
            { name: 'basicstyles', items : [ 'Bold','Italic','Strike','-','RemoveFormat' ] },
            { name: 'styles',      items : [ '-','Format' ] },
            { name: 'clipboard',   items : [ 'Cut','Copy','Paste', 'PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'insert',      items : [ 'Image','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
            { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote' ] },
            { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
            { name: 'tools',       items : [ 'Maximize' ] }
        ]
    });
    var valorEditor = <?php echo $valor; ?>;
    if( valorEditor != -1 ){
        editor.setData( valorEditor );
    }


    $("#insertNoTela").submit( function ( e ) {

        e.preventDefault();
        var arrDados = $( this ).serializeArray();

        // Percorre todos os textareas do form
        $.each($(this).find( "textarea" ), function ( i, v ) {
            // atribui a variavel editor a instancia do ckeditor pega pelo id do textarea
            // onde v é todo o objeto textarea
            editor = CKEDITOR.instances[$( v ).attr( 'id' )].getData();
            //console.log( editor );
            // percorre todo o array arData
            $.each(arrDados, function (indice, value) {
                // e pergunta se o name do arData é o mesmo do textarea
                if ( value['name'] == $( v ).attr( 'name' ) ) {
                    // caso seja, no indice que bateu de o name do arData e o name do textarea são iguas
                    // ele atribui o valor retornado pela variavel editor.
                    arrDados[indice]['value'] = editor;
                }
            });
        });


        $.ajax({
            url: $( this ).attr( 'action' ),
            type: 'POST',
            data: arrDados,
            success: function ( res ) {
                if ( res == 'error' ) {
                    console.log( res );
                    // $( '.retorno' ).html( res );
                    // Mostra a mensagem com os dados retornados
                    $( "#mensagens" ).removeClass( 'hidden' ).removeClass( 'alert-success' ).addClass( 'alert-danger' ).show();
                    $( "#mensagens" ).html( res );
                } else {
                    console.log( res );
                    // $( '.retorno' ).html( res );
                    // Mostra a mensagem com os dados retornados
                    $( "#mensagens" ).removeClass( 'hidden' ).show();
                    $( "#mensagens" ).html( res );
                }
            }
        });

    });
</script>
