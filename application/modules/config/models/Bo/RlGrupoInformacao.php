<?php

class Config_Model_Bo_RlGrupoInformacao extends App_Model_Bo_Abstract
{
    /**
     * @var Config_Model_Dao_RlPerfilInformacao
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_RlGrupoInformacao();
        parent::__construct();
    }


    public function listGrupoInfo($id_grupo = null, $id_info = null)
    {
        return $this->_dao->listGrupoInfo($id_grupo, $id_info);
    }

    public function persiste($uuid,$idGrupo,$idPessoa,$idInformacao){
        $rowPai = null;
        if($uuid){
            $rowPai = $this->_dao->find($uuid);
            if($rowPai->count()==0) {
                throw new Exception("Pessoa não encontrada");
            }
            $rowPai = $rowPai->current();
        } else {
            $rowPai  = $this->_dao->createRow();
            $uuid = UUID::v4();
            $rowPai->id     = $uuid;
        }

        if($idGrupo){
            $rowPai->id_grupo = $idGrupo;
        }
        if($idPessoa){
            $rowPai->id_pessoa = $idPessoa;
        }
        if($idInformacao){
            $rowPai->id_info = $idInformacao;
        }
        $rowPai->save();
        return $uuid;
    }

    /**
     * Cria o relacionamento entre um grupo e uma informação.
     *
     * @param string $idGrupo uuid do grupo.
     * @param string $idInfo uuid da informação.
     * @param string $idPessoa uuid da pessoa à qual o relacionamento pertence.
     */
    public function relacionaGrupoInformacao($idGrupo, $idInfo, $idPessoa)
    {
        $this->persiste(null, $idGrupo, $idPessoa, $idInfo);
    }
}
