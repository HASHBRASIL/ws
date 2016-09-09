<?php

/**
 * Created by PhpStorm.
 * User: fernando
 * Date: 15/01/2016
 * Time: 15:30
 */
class RlGrupoItem extends Base
{

    /** 
     * @var string
     */
    protected $table = "rl_grupo_item";

    function criaRlGrupoItem ($id, $id_grupo, $id_item) {
        $stmt = $this->dbh->prepare("INSERT INTO rl_grupo_item ( id, id_grupo, id_item )
                                    VALUES ( :id,:id_grupo,:id_item )");
        $stmt->bindValue('id',$id);
        $stmt->bindValue('id_grupo',$id_grupo);
        $stmt->bindValue('id_item',$id_item);

        $stmt->execute();

        return $id;
    }

    function listPublicacao($idItem) {
        $stmt = $this->dbh->prepare("select * from rl_grupo_item where id_item = :item");
        $stmt->bindParam('item', $idItem);

        $stmt->execute();

        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }

    function getPublicacao($idItem,$idGrupo) {
        $stmt = $this->dbh->prepare("select * from rl_grupo_item where id_item = :item and id_grupo = :grupo");
        $stmt->bindParam('item', $idItem);
        $stmt->bindParam('grupo', $idGrupo);
        $stmt->execute();

        $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $ret;
    }

}