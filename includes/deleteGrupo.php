<?php
    require_once "connect.php";

    $id = ( isset( $_POST["id"] ) ) ? $_POST["id"] : FALSE;
    $queryDeleteGrupo = $dbh->prepare( "DELETE FROM tb_grupo WHERE id  = :id;" );
    $dbh->beginTransaction();

    try {

        $queryDeleteGrupo->bindParam( ':id', $id );
        $queryDeleteGrupo->execute();

        // $dbh->commit();
        echo "Sucesso!";

    } catch ( PDOException $e ) {
        $dbh->rollBack();
        # var_dump($e);
        echo "error";
    }
?>