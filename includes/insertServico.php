<?php
    # Não passar alguns dados vazios:

    require_once "connect.php";
    require_once "UUID.php";

    $queryTbServico      = $dbh->prepare( "INSERT INTO tb_servico ( id, descricao, fluxo, metanome, nome, id_pai, id_tib, visivel, ordem )
                                                           VALUES ( :id, :descricao, :fluxo, :metanome, :nome, :id_pai, :id_tib, :visivel, :ordem );" );
    $queryTbServicoMD    = $dbh->prepare( "INSERT INTO tb_servico_metadata (id, metanome, valor, id_servico)
                                                                     VALUES(:id, :metanome, :valor, :id_servico);" );

    $dbh->beginTransaction();

    try{

        $tokenServico = UUID::v4();
        $queryTbServico->bindValue(':id',        $tokenServico);
        $queryTbServico->bindValue(':descricao', $_POST['servico']['descricao']);
        $queryTbServico->bindValue(':fluxo',     $_POST['servico']['fluxo']);
        $queryTbServico->bindValue(':metanome',  $_POST['servico']['metanome']);
        $queryTbServico->bindValue(':nome',      $_POST['servico']['nome']);
        $queryTbServico->bindValue(':id_pai',    ($_POST['servico']['servico_pai'] == 'null') ? NULL : $_POST['servico']['servico_pai'] );
        $queryTbServico->bindValue(':id_tib',    ($_POST['servico']['tib'] == 'null')? NULL:$_POST['servico']['tib']);
        $queryTbServico->bindValue(':visivel',   $_POST['servico']['visivel']);
        $queryTbServico->bindValue(':ordem',     (empty($_POST['servico']['ordem']))?NULL:$_POST['servico']['ordem'] );
        $queryTbServico->execute();
        echo "Criou Servico -> ". $_POST['servico']['nome']."\n";

        if(isset($_POST['metadata'])){
            foreach($_POST['metadata'] as $key => $value){

                $tokenMetadata = UUID::v4();

                $queryTbServicoMD->bindParam(':id',         $tokenMetadata);
                $queryTbServicoMD->bindParam(':metanome',   $value['metanome']);
                $queryTbServicoMD->bindParam(':valor',      $value['valor']);
                $queryTbServicoMD->bindParam(':id_servico', $tokenServico);
                $queryTbServicoMD->execute();
                echo "Criou METADATA -> ". $value['metanome'] . ' : '.$value['valor']."\n";
            }
        }
        $dbh->commit();
        echo "\nCOD2: Dados salvos com sucesso.\n";

    }catch( PDOException $e ){

        $dbh->rollBack();
        // echo "\n".$e->getMessage()."\n";
        // print_r($e);
        var_dump($e);
        echo "error";

    }

