<?php
    require_once "connect.php";

    $idMaster    = ( empty( $_GET['master'] ) )        ? $SERVICO['id_tib']                    : $_GET['master'];
    $fluxo       = ( empty( $_GET['fluxo'] ) )         ? $SERVICO['fluxo']                     : $_GET['fluxo'];
    $ibmetadata  = ( empty( $_GET['ws_ibmetadata'] ) ) ? $SERVICO['metadata']['ws_ibmetadata'] : $_GET['ws_ibmetadata'];

    if( $idMaster != -1 ){

        $selectTemplate = $dbh->prepare( "SELECT * from tp_itembiblioteca WHERE id_tib_pai = :idMaster" );
        $selectTemplate->bindParam( ':idMaster', $idMaster );
        $selectTemplate->execute();

        $arTemplate = $selectTemplate->fetchAll();

        try {
            $selectTemplate->execute();
            $arTemplate = $selectTemplate->fetchAll();
        } catch ( PDOException $e ) {
            var_dump($e);
        }
    }

?>
<div class="row wrapper">
    <div class="page-header">
        <h1><?php echo $SERVICO['nome']; ?></h1>
        <span><?php echo $SERVICO['descricao']; ?></span>
    </div>
    <div class="col-md-12 content">
        <div class="form-header">
            <!-- <h1>Título do Formuláro <span>Breve descriçao do formulário</span></h1> -->
            <form action="includes/insertDataMaster.php" id="formInsert" method="POST" accept-charset="utf-8">
                <?php
                    if ( isset( $arTemplate ) ) {
                        foreach ( $arTemplate as $item ) {
                            echo '<div class="form-group">';
                            echo '<label>' . $item['nome'] . '</label>';
                            echo createFormElement( $item['tipo'], '', $item['descricao'], ['id' => $item['id'], 'class' => 'form-control ckeditor', 'name' => $item['id']] );
                            echo '</div>';
                        }
                    }
                ?>
                <div class="row">
                    <button type="submit" id="submitinsertDataMaster" class="btn btn-success btn-sm pull-right">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    var tokenMaster = <?php echo json_encode($idMaster); ?>;
    var fluxo       = '<?php echo ucfirst($fluxo); ?>'

    if( fluxo != -1 ){
        $("#submitinsertDataMaster").text( fluxo );
    }else{
        $("#submitinsertDataMaster").remove();
    }

    $("#formInsert").submit( function ( e ) {

        e.preventDefault();

        arData = $( this ).serializeArray();

        // Percorre todos os textareas do form
        $.each( $( this ).find( "textarea" ), function ( i, v ) {
            // atribui a variavel editor a instancia do ckeditor pega pelo id do textarea
            // onde v é todo o objeto textarea
            editor = CKEDITOR.instances[$( v ).attr( 'id' )].getData();

            // percorre todo o array arData
            $.each(arData, function ( indice, value ) {
                // e pergunta se o name do arData é o mesmo do textarea
                if ( value['name'] == $( v ).attr( 'name' )) {
                    // caso seja, no indice que bateu de o name do arData e o name do textarea são iguas
                    // ele atribui o valor retornado pela variavel editor.
                    arData[indice]['value'] = editor;
                }

            });

        });

        $.ajax({
            url: 'includes/insertDataMaster.php',
            type: 'POST',
            data: { data: arData, idMaster: tokenMaster },
            success: function (result) {

                if ( result == 'error' ) {
                    // console.log( result );
                    // Mostra a mensagem com os dados retornados
                    $( "#mensagens" ).removeClass( 'hidden' ).removeClass( 'alert-success' ).addClass( 'alert-danger' ).show();
                    $( "#mensagens" ).text( result );
                } else {
                    // console.log(result);
                    // Mostra a mensagem com os dados retornados
                    $( "#mensagens" ).removeClass( 'hidden' ).show();
                    $( "#mensagens" ).text( result );
                }

            }
        });
    });
</script>
