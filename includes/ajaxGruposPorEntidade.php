<?php

    require_once "connect.php";

    $idEntidade   = ( isset( $_POST['id_entidade'] ) ) ? $_POST['id_entidade'] : FALSE;
    $selectGrupos = $dbh->prepare( "SELECT * FROM tb_grupo WHERE id_pai = :idEntidade ORDER BY nome ASC" );

    if( $idEntidade ){

        $selectGrupos->bindParam( ':idEntidade', $idEntidade );
        $res = $selectGrupos->execute();

        try {
            if( $res ){

                $grupos = $selectGrupos->fetchAll( PDO::FETCH_ASSOC );
                echo json_encode( $grupos );

            }else{
                echo 'error';
            }

        } catch ( PDOException $e ) {
            // echo $e->getMessage();
            echo "error";
        }

    }else{
        echo "error";
    }

?>