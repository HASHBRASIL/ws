<?php

    require_once "connect.php";
    require_once "UUID.php";

    // echo "<pre>";

    // foreach($_POST as $key => $val){
    //  var_dump($val);
    // }

    // var_dump($_POST);
    // die();

    $queryMaster   = $dbh->prepare("INSERT INTO tb_itembiblioteca ( id, dt_criacao, id_tib ) VALUES ( :id, (select current_timestamp), :id_tib );");
    $queryChildren = $dbh->prepare("INSERT INTO tb_itembiblioteca ( id, dt_criacao, valor, id_ib_pai, id_tib, dtype ) VALUES ( :id, (select current_timestamp), :valor, :id_ib_pai, :id_tib,'TbItemBiblioteca' );");

    $dbh->beginTransaction();

    try {
        $tokenIDMaster = UUID::v4();

        $queryMaster->bindParam(':id', $tokenIDMaster);
        $queryMaster->bindParam(':id_tib', $_POST['idMaster']);

        $queryMaster->execute();

        // var_dump('MASTER');

        foreach($_POST['data'] as $value){
            $tokenIDChild = UUID::v4();

            // var_dump($value);
            // var_dump( $value['name'] );

            $queryChildren->bindParam(':id', $tokenIDChild);
            $queryChildren->bindParam(':valor', $value['value']);
            $queryChildren->bindParam(':id_ib_pai', $tokenIDMaster);
            $queryChildren->bindParam(':id_tib', $value['name']);
            $queryChildren->execute();
        }

        $dbh->commit();
        echo "Dados salvos com sucesso.\n";

    } catch ( PDOException $e ) {
        $dbh->rollBack();
        // var_dump($e);
        echo "error";
    }
