<?php require_once "functions.php"; ?>

<form action="includes/updateFormInline.php" method="post" id="formInlineUpdate" accept-charset="utf-8">
    <div class="form-group-one-unit">
        <?php
            // echo "numero do array: ".count( $data );
            foreach ( $data as $key => $value ) {

                $name   = str_replace(' ', '', $value['nome']);
                $string = "<div class='form-group'><label for=''>%s</label>%s</div>";

                if( $value['tipo'] == 'textarea' ){

                    $AtributosInput  = array( "class" => 'form-control ckeditor updateForm updateFormTextArea', "id" => 'editor_'.strtolower( $name ), "name" => strtolower( $name ), "data-idIb" => $value['id_ib'] );
                    $input           = createFormElement( $value['tipo'], $value['valor'], '', $AtributosInput );
                    $textArea        = sprintf( $string, $value['nome'], $input );

                    echo "<div class='row'>{$textArea}</div>";

                }else{

                    $string          = "<div class='form-group'><label for=''>%s</label>%s</div>";
                    $AtributosInput  = array( "class" => 'form-control updateForm', "id" => $value['id_ib'], "name" => strtolower( $name ), "data-idIb" => $value['id_ib'] );
                    $input           = createFormElement( $value['tipo'], $value['valor'], '', $AtributosInput );
                    $campo           = sprintf( $string, $value['nome'], $input );

                    echo "<div class='row'>{$campo}</div>";
                }
            }
        ?>
    </div>

    <div class="row">
        <button class="btn btn-success btn-sm pull-right" id="salvarFormInline" type="submit" >Salvar</button>
    </div>
</form>

<script>
    // Procura dentro do formulario todas as classes passadas no .find()
    $( '#formInlineUpdate' ).find( '.updateFormTextArea' ).each( function( i, v ){
        // quando encontra, pega o id e usa como parametro para
        // transformar o textarea em ckeditor
        CKEDITOR.replace( $( this ).attr( 'id' ), {
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
            ],
            height: 90
        });
    });

    $("#formInlineUpdate").submit( function ( e ) {

        e.preventDefault();
        var arrDados = $( this ).serializeArray();

        // Percorre dentro do formulario procurando todas as classes .updateForm
        $( '#formInlineUpdate' ).find( '.updateForm' ).each( function( i, v ){
            // Percorre dentro do array de dados
            $.each( arrDados, function( index, value ){
                // Pergunta se o atributo name do value e igual ao
                // array de dados
                if( $( v ).attr( 'name' ) == arrDados[index]['name'] ){
                    // se passar cria mais um indece no array e atribui o valor encontrado
                    arrDados[index]['idib'] = $( v ).attr('data-idIb');
                    return false;
                }
            })
        });


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
            data: { data: arrDados, },
            success: function ( res ) {
                if ( res == 'error' ) {
                    console.log( res );
                    // Mostra a mensagem com os dados retornados
                    //$( "#mensagens" ).removeClass( 'hidden' ).removeClass( 'alert-success' ).addClass( 'alert-danger' ).show();
                    //$( "#mensagens" ).text( result );
                } else {
                    console.log( res );
                    // Mostra a mensagem com os dados retornados
                    //$( "#mensagens" ).removeClass( 'hidden' ).show();
                    //$( "#mensagens" ).text( result );
                }
            }
        });

    });
</script>