<?php

    require_once 'connect.php';

    $query = $dbh->prepare(
        "SELECT
            RL.id,
            RL.id_grupo,
            RL.id_item,
            IB.id_tib
        FROM rl_grupo_item AS RL
            LEFT OUTER JOIN tb_itembiblioteca AS IB ON ( RL.id_item = IB.id )
        WHERE RL.id_grupo = :id_grupo"
    );

    $query->bindParam(":id_grupo", $_POST['id_grupo']);

    $query->execute();

    $arOrdenado = array();

    foreach( $query->fetchAll( PDO::FETCH_ASSOC ) as $key => $value ){
        $arOrdenado[$value['id_tib']][] = $value;
    }

    echo json_encode( $arOrdenado );

?>