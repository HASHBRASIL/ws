<?php

    include_once "connect.php";
    include_once "UUID.php";

    $tokemIDTPTemplate = UUID::v4();

    $idTib        = $_POST['idTib'];
    $idTpTemplate = $_POST['dadoSelect'];
    $dadosEditor  = urldecode( $_POST['dadosEditor'] );

    $dbh->beginTransaction();

    try{

        # vai inserte
        $tokemIDTPTemplate = UUID::v4();
        $dadosEditor       = urldecode( $_POST['dadosEditor'] );

        // echo "-----------\n";
        // echo "Dados insert: \n";
        // echo "-----------\n";
        // echo "Tokem: {$tokemIDTPTemplate}\n";
        // echo "HTML: {$dadosEditor}\n";
        // echo "IDTIB: {$idTib}\n";
        // echo "IDTEMPLATE: {$idTpTemplate}\n";
        // echo "-----------\n";

        $insertCMSTPTemplate = $dbh->prepare( "INSERT INTO cms_template VALUES( :id, :html, :idTib, :idTemplate )" );
        $insertCMSTPTemplate->bindParam(":id", $tokemIDTPTemplate);
        $insertCMSTPTemplate->bindParam(":html", $dadosEditor);
        $insertCMSTPTemplate->bindParam(":idTib", $idTib);
        $insertCMSTPTemplate->bindParam(":idTemplate", $idTpTemplate);

        $insertCMSTPTemplate->execute();

         $dbh->commit();

        echo "Dados salvos com sucesso.\n";

    }catch( PDOException $e ){
        // echo $e->getMessage();
        $dbh->rollBack();
        echo "error";
    }
