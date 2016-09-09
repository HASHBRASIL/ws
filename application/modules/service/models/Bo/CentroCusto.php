<?php
class Service_Model_Bo_CentroCusto extends App_Model_Bo_Abstract
{

    /**
     * @var Service_Model_Dao_CentroCusto
     */
    protected $_dao;

    public function __construct()
    {
    	$this->_hasWorkspace = true;
    	$this->_getRegistersWithoutWorkspace = true;
        $this->_dao = new Service_Model_Dao_CentroCusto();
        parent::__construct();
    }

    public function getAutocomplete($term, $ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        $where = null;
        if($ativo){
            $where = array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        $where = $where + array('cec_oculta = ?'=> 0);
        return $this->_dao->getAutocomplete($term, $chave, $valor, $where, $ordem, $limit);
    }

    public function getCentroByService($idCentroCusto, $idServico)
    {
        if(!empty($idCentroCusto)){
            $criteria      = array(
                    "ativo = ?"     => App_Model_Dao_Abstract::ATIVO,
                    "cec_id in (?)" => $idCentroCusto
            );
            return $this->find($criteria);
        }else if( !empty($idServico) ){
            $servicoCentro = new Service_Model_Bo_ServicoCentroCusto();
            return $servicoCentro->getCentroCustoByServico($idServico);

        }
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->cec_descricao)){
            App_Validate_MessageBroker::addErrorMessage("O campo descrição é obrigatório.");
            return false;
        }else{
            if($this->_dao->stringEquals('cec_descricao', $object->cec_descricao, $object->cec_id)){
                App_Validate_MessageBroker::addErrorMessage("Está descrição já existe.");
                return false;
            }
        }
        return true;
    }
    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->cec_oculta)){
            $object->cec_oculta = 0;
        }

        if(empty($object->cec_operacional)){
            $object->cec_operacional = 0;
        }

    }

}