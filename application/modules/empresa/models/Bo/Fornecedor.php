<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_Model_Bo_Fornecedor extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Dao_Fornecedor
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_Fornecedor();
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