<?php

    require_once "connect.php";

    $id = ( isset( $_POST['id'] ) ) ? $_POST['id'] : FALSE;

    $ib = $dbh->prepare("
        SELECT
            tib.metanome,tib.nome, ib.*
        FROM
            tp_itembiblioteca tib JOIN tb_itembiblioteca ib ON (tib.id = ib.id_tib)
        WHERE ib.id_ib_pai = :id "
    );

    try {

        if( $ib ){

            $ib->bindParam( ':id', $id );

            $fetchQuery = $ib->fetchAll();

            $arHtml = array();

            foreach( $fetchQuery as $key => $value ){

                foreach( $value as $chave => $val ){
                    $value[$chave] = htmlentities($val);
                }

                $arHtml[$value['id_ib_pai']][] = $value;
            }

            echo json_encode( $arHtml );


        }else{
            echo "ERRO\n";
        }

    } catch ( PDOException $e ) {
        echo $e->getMessage();
    }

    function cmp( $c, $d ){
        return strcmp($c->conteudo, $d->conteudo);
    }

?>