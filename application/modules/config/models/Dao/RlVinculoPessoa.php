<?php

class Config_Model_Dao_RlVinculoPessoa extends App_Model_Dao_Abstract
{
    protected $_name          = "rl_vinculo_pessoa";
    protected $_primary       = "id";
    //protected $_namePairs   = "nome";
    
    protected $_rowClass = 'Config_Model_Vo_RlVinculoPessoa';
    
    public function delVinculoById($idVinculo) {
        $qry = $this->_db->prepare('DELETE FROM rl_vinculo_pessoa WHERE id = :id_vinculo');
        $qry->bindParam( ':id_vinculo', $idVinculo );
        
        return $qry->execute();
    }

    public function getVinculoByClsPesGrp($idcls,$idpes,$idgrp) {
        $select = $this->_db->select()->from(array($this->_name))
            ->where('id_classificacao = ?',$idcls)
            ->where('id_pessoa = ?',$idpes)
            ->where('id_grupo = ?',$idgrp);
            return $this->_db->query($select)->fetchAll();
    }

}