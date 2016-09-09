<?php
    require_once "connect.php";
    require_once "functions.php";

    $idGrupo  = ( isset( $_POST['idGrupo'] ) ) ? $_POST['idGrupo'] : FALSE;
    $nomeHash = new RlGrupoPessoa();
    $nomeHash->getNomeHashByIDGrupo( $idGrupo );

    try {
        if( $nomeHash ){
            echo $nomeHash[0]['nomehash'];
        }else{
            echo 'error';
        }
    } catch ( PDOException $e ) {
        // echo $e->getMessage();
        echo "error\n";
    }
