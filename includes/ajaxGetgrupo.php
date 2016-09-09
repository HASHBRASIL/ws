<?php

    require_once "connect.php";
    require_once "functions.php";

    $id          = ( isset( $_POST['id'] ) ) ? $_POST['id'] : FALSE;
    $id_grupo    = ( isset( $_POST['id_grupo'] ) ) ? $_POST['id_grupo'] : FALSE;
    $idGrupoArea = ( isset( $_POST['id_grupoarea'] ) ) ? $_POST['id_grupoarea'] : FALSE;

    if( $id_grupo ){
        header('Content-Type: application/json');
        echo json_encode( getGrupoByID( $dbh, $id_grupo ) );
    }

    if( $id ){
        $Grupoquery=$dbh->prepare("
            WITH RECURSIVE tb_todos_grupos (id,nome,id_pai) as
            (
            SELECT id,nome,id_pai FROM tb_grupo WHERE id = :id
            UNION ALL
            SELECT tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai FROM tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
            )
            SELECT * FROM tb_todos_grupos"
        );

        $Grupoquery->bindParam( ':id', $id );
        $retorno = $Grupoquery->execute();

        try {
            if( $retorno ){

                $fetchQuery = $Grupoquery->fetchAll();
                echo json_encode( $fetchQuery );
            }else{
                echo 'ERRO';
            }
        } catch ( PDOException $e ) {
            // echo $e->getMessage();
            echo "error";
        }
    }

    if( $idGrupoArea ){
        $Grupoareaquery = $dbh->prepare("
            WITH RECURSIVE tb_todos_grupos (id,nome,id_pai) as
            (
            SELECT id,nome,id_pai FROM tb_grupo WHERE id = :idGrupoArea
            UNION ALL
            SELECT tb_grupo.id, tb_grupo.nome, tb_grupo.id_pai FROM tb_grupo INNER JOIN tb_todos_grupos ON tb_grupo.id_pai = tb_todos_grupos.id
            )
            SELECT * FROM tb_todos_grupos"
        );

        $Grupoareaquery->bindParam( ':idGrupoArea', $idGrupoArea );
        $retornoAQuery = $Grupoareaquery->execute();

        try {
            if( $retornoAQuery ){
                $fetchQuery = $Grupoareaquery ->fetchAll();
                echo json_encode( $fetchQuery );
            }else{
                echo 'ERRO';
            }
        } catch ( PDOException $e ) {
            // echo $e->getMessage();
            echo "error";
        }
    }
?>