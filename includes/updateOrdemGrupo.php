<?php
require_once "connect.php";
require_once "UUID.php";

$valor            = $_POST['valor'];
$id               = $_POST['id'];
$arOrdencao       = $_POST['order'];


$queryUpdateOrdem     = $dbh->prepare(" UPDATE tb_grupo SET  id_pai = :id_pai  WHERE id = :id");
$queryDelete          = $dbh->prepare(" DELETE FROM tb_grupo_metadata where id_grupo = :order and metanome = 'ws_ordem' ");
$queryInsert          = $dbh->prepare(" INSERT INTO tb_grupo_metadata VALUES ( :id,:metanome,:valor,:id_grupo )");

$dbh->beginTransaction();

try {

    $queryUpdateOrdem->bindParam(':id', $id);
    $queryUpdateOrdem->bindParam(':id_pai', $valor);
    $queryUpdateOrdem->execute();

    #deletando tudo (filhos e pais)
    foreach($arOrdencao as $key =>$value){
        foreach ($value['filhos'] as $chave => $valor) {
            $queryDelete->bindValue(':order',$valor['id_grupo']);
            $queryDelete->execute();

        }
        $queryDelete->bindValue(':order',$value['id_grupo']);
        $queryDelete->execute();

    }

    #Inserindo (filhos e pais)
    foreach($arOrdencao as $key =>$value){
        $tokenIDMaster = UUID::v4();
        foreach ($value['filhos'] as $chave => $valor) {
            $tokenID= UUID::v4();
            $queryInsert->bindValue(':id',$tokenID);
            $queryInsert->bindValue(':metanome','ws_ordem');
            $queryInsert->bindValue(':valor',$valor['valor']);
            $queryInsert->bindValue(':id_grupo',$valor['id_grupo']);
            $queryInsert->execute();

        }
        $queryInsert->bindValue(':id',$tokenIDMaster);
        $queryInsert->bindValue(':metanome','ws_ordem');
        $queryInsert->bindValue(':valor',$value['valor']);
        $queryInsert->bindValue(':id_grupo',$value['id_grupo']);
        $queryInsert->execute();

    }


    $dbh->commit();
} catch (PDOException $e) {
    $dbh->rollBack();
    echo "error";
}
