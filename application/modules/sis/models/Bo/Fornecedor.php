<?php
class Sis_Model_Bo_Fornecedor extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Fornecedor
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Fornecedor();
        parent::__construct();
    }

    public function getFornecedorByService($idFornecedor, $idServico)
    {
        if(!empty($idFornecedor)){
            $criteria         = array(
                                   "ativo = ?" => App_Model_Dao_Abstract::ATIVO,
                                   "id in(?)"  => $idFornecedor
                                );
            return $this->find($criteria);
        }else if ( !empty($idServico) ){
            $servicoEmpresa = new Service_Model_Bo_ServicoEmpresa();
            return $servicoEmpresa->getFornecedorByServico($idServico);
        }
    }

}