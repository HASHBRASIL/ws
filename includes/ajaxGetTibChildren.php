<?php

    require_once "connect.php";

    $queryGetTib = $dbh->prepare(
        "SELECT
            tib.*, vis.valor AS visivel, ordemLista.valor AS lista, ordem.valor AS ordem
        FROM tp_itembiblioteca tib
            LEFT OUTER JOIN (SELECT id_tib,valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_visivel') vis ON (tib.id = vis.id_tib)
            LEFT OUTER JOIN (SELECT id_tib,valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordemLista') ordemLista ON (tib.id = ordemLista.id_tib)
            LEFT OUTER JOIN (SELECT id_tib,valor FROM tp_itembiblioteca_metadata WHERE metanome = 'ws_ordem') ordem ON (tib.id = ordem.id_tib)
        WHERE tib.id_tib_pai = :id_tib_pai
        ORDER BY ordem;"
    );

    $queryGetTib->bindParam( ':id_tib_pai', $_POST['id_tib_pai'] );

    try {

        $queryGetTib->execute();
        $dataTib = $queryGetTib->fetchAll( PDO::FETCH_ASSOC );

        echo JSON_encode( $dataTib );

    } catch ( PDOException $e ) {
        echo "error\n";

    }


?>