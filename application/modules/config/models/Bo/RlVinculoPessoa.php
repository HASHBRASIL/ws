<?php

class Config_Model_Bo_RlVinculoPessoa extends App_Model_Bo_Abstract
{
    
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_RlVinculoPessoa();
        parent::__construct();
    }

    public function persiste($uuid,$idClassificacao,$idPessoa,$idVinculado,$idGrupo,$dtInicio,$dtFim) {
        $rowPai = null;
        if($uuid){
            $rowPai = $this->_dao->find($uuid);
            if($rowPai->count()==0) {
                throw new Exception("Vínculo não encontrada");
            }
            $rowPai = $rowPai->current();
        } else {
            $rowPai  = $this->_dao->createRow();
            $uuid = UUID::v4();
            $rowPai->id = $uuid;
        }
        if($idClassificacao){
            $rowPai->id_classificacao = $idClassificacao;
        }
        if($idPessoa){
            $rowPai->id_pessoa = $idPessoa;
        }
        if($idVinculado){
            $rowPai->id_vinculado = $idVinculado;
        }
        if($idGrupo){
            $rowPai->id_grupo = $idGrupo;
        }
        if($dtInicio){
            $rowPai->datainicio = $dtInicio;
        }
        if($dtFim){
            $rowPai->datafim = $dtFim;
        }
        $rowPai->save();
        return $uuid;
    }
    
    public function delVinculoById($idVinculo) {
        return $this->_dao->delVinculoById($idVinculo);
    }

    public function getVinculoByClsPesGrp($idcls,$idpes,$idgrp) {
        return $this->_dao->getVinculoByClsPesGrp($idcls,$idpes,$idgrp);
    }
}