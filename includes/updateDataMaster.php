<?php

    require_once "connect.php";
    require_once "UUID.php";

    $querySel = $dbh->prepare("SELECT * from tb_itembiblioteca WHERE id_tib = :idTib AND id_ib_pai = :idIb");
    $queryUpd = $dbh->prepare("UPDATE tb_itembiblioteca SET valor = :valor WHERE id_tib = :id_tib AND id_ib_pai = :id_ib_pai");
    $queryIns = $dbh->prepare("INSERT INTO tb_itembiblioteca ( id, dt_criacao, valor, id_ib_pai, id_tib, dtype ) VALUES ( :id, (select current_timestamp), :valor, :id_ib_pai, :id_tib,'TbItemBiblioteca' )");

    $dbh->beginTransaction();

    try {

        $idIb = $_POST['idData'];


        foreach ($_POST as $key => $value) {

            if (( $key != 'idData' ) && ( $key != 'idMaster' )) {

                $querySel->bindParam(":idTib",$key);
                $querySel->bindParam(":idIb",$idIb);

                $querySel->execute();

                $lstItems = $querySel->fetchAll(PDO::FETCH_ASSOC);

                if(count($lstItems)==0) {
                    $queryIns->bindParam(':id', UUID::v4());
                    $queryIns->bindParam(':valor', $value);
                    $queryIns->bindParam(':id_ib_pai', $idIb);
                    $queryIns->bindParam(':id_tib', $key);
                    $queryIns->execute();
                } else {
                    $queryUpd->bindParam(':valor', $value);
                    $queryUpd->bindParam(':id_ib_pai', $idIb);
                    $queryUpd->bindParam(':id_tib', $key);
                    $queryUpd->execute();
                }
            }
        }
        $dbh->commit();
        echo "Dados salvos com sucesso.";
    } catch ( Exception $e ) {
        $dbh->rollBack();
        // var_dump($e);
        echo "error";
    }

