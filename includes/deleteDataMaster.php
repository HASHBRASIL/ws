<?php

    require_once "connect.php";
    require_once "UUID.php";

    $id = $_POST["id"];
    $queryDelete = $dbh->prepare(
        "DELETE FROM
            tb_itembiblioteca
        WHERE
            id IN ( SELECT id FROM tb_itembiblioteca WHERE id_ib_pai = :id )
        OR
            id = :id;"
    );
    $dbh->beginTransaction();

    try {
        # var_dump( $_POST );
        $queryDelete->bindParam( ':id', $id );
        $queryDelete->execute();

        $dbh->commit();

    } catch ( PDOException $e ) {
        $dbh->rollBack();
        # var_dump($e);
        echo "error";
    }
?>
