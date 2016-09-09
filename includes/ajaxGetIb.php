<?php

    require_once "connect.php";

    $id = ( isset( $_POST['id'] ) ) ? $_POST['id'] : FALSE;

    $ib = $dbh->prepare("
        SELECT ib.valor as conteudo, timb.valor, ib.id_ib_pai
        FROM tb_itembiblioteca ib
            JOIN tp_itembiblioteca tib ON (ib.id_tib = tib.id)
            JOIN tp_itembiblioteca_metadata timb ON (timb.id_tib = tib.id)
        WHERE tib.id_tib_pai = :id AND timb.metanome = 'ws_ordemLista'
        ORDER BY ib.id_ib_pai, timb.valor"
    );



    try {
        if( $id ){

            $ib->bindParam( ":id", $id );
            $ib->execute();


            $fetchQuery = $ib->fetchAll();
            $arHtml = array();

            foreach( $fetchQuery as $key => $value ){

                foreach( $value as $chave => $val ){

                    $value[$chave] = htmlentities( $val );

                }

                $arHtml[$value['id_ib_pai']][] = $value;
            }

            echo json_encode( $arHtml );

        }else{
            echo 'ERRO';
        }

    } catch ( PDOException $e ) {
        // echo $e->getMessage();
        echo "error\n";
    }

?>