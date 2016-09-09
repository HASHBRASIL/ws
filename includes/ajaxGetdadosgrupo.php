<?php
    require_once "connect.php";
    require_once "functions.php";



    $idGrupo    = ( isset( $_POST['idGrupo'] ) ) ? $_POST['idGrupo'] : FALSE;
    $dadosGrupo = getDadosGrupoByID( $idGrupo, $dbh, $param = PDO::FETCH_ASSOC );

    try {
        if( $dadosGrupo ){
            echo json_encode( $dadosGrupo[0] );
        }else{
            echo 'error';
        }
    } catch ( PDOException $e ) {
        echo $e->getMessage();
        echo "error\n";
    }
?>