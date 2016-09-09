<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 01/07/2013
 */
class Service_Model_Bo_TipoOrcamento extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_TipoOrcamento
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_TipoOrcamento();
        parent::__construct();
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->nome)){
            App_Validate_MessageBroker::addErrorMessage("Preencha o campo nome do tipo de orçamento.");
            return false;
        }
        $criteria = array('nome = ?' => $object->nome, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        if(!empty($object->id_tp_orcamento)){
            $criteria = array('nome = ?' => $object->nome, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_tp_movimento <> ?' => $object->id_tp_orcamento);
        }

        $tipo = $this->find($criteria);
        if(count($tipo)){
            App_Validate_MessageBroker::addErrorMessage("Já existe este tipo de orçamento.");
            return false;
        }
        return true;
    }
}