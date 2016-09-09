<?php
    include "connect.php";
    // var_dump( $_POST );exit;
    $master = $_POST['id_master'];
    $idItem = $_POST['id'];
    $fluxo  = ( empty( $_GET['fluxo'] ) ) ? -1 : $_GET['fluxo'];

    $queryMasterData = $dbh->prepare(
        "SELECT
            ib.id_ib_pai, ib.id AS id_ib,   ib.valor, tib.id AS id_tib, tib.nome,
            tib.tipo,vis.valor  AS visivel, ordem.valor      AS ordem,  ordemLista.valor AS ordemlista, tib.descricao
        FROM
            tb_itembiblioteca ib
                INNER JOIN tp_itembiblioteca tib ON (ib.id_tib = tib.id)
                LEFT OUTER JOIN
                (
                    SELECT
                        id_tib,valor, id_tib_pai
                    FROM
                        tp_itembiblioteca_metadata
                    WHERE
                        metanome = 'ws_visivel' AND tp_itembiblioteca_metadata.id_tib_pai = :master
                ) vis ON ( tib.id = vis.id_tib )

                LEFT OUTER JOIN
                (
                    SELECT id_tib,valor, id_tib_pai
                    FROM tp_itembiblioteca_metadata
                    WHERE metanome = 'ws_ordemLista' AND tp_itembiblioteca_metadata.id_tib_pai = :master
                ) ordemLista ON ( tib.id = ordemLista.id_tib )

                LEFT OUTER JOIN
                (
                    SELECT id_tib,valor, id_tib_pai
                    FROM tp_itembiblioteca_metadata
                    WHERE metanome = 'ws_ordem' AND tp_itembiblioteca_metadata.id_tib_pai = :master
                ) ordem ON ( tib.id = ordem.id_tib )
        WHERE
            ib.id_ib_pai = :idItem AND ordemLista IS NOT NULL
        ORDER BY ordem;"
    );

    $queryMasterData->bindParam(':idItem', $idItem);
    $queryMasterData->bindParam(':master', $master);

    try {
        $queryMasterData->execute();
        $data = $queryMasterData->fetchAll( PDO::FETCH_ASSOC );

        include_once "buildFormInline.php";

    } catch ( PDOException $e ) {
        $dbh->rollBack();
        var_dump( $e );
    }
?>



