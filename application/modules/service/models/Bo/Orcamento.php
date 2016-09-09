<?php
/**
 * @author: Ellyson de Jessus Silva
 * @since: 01/07/2013
 */

class Service_Model_Bo_Orcamento extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_Orcamento
     */
    protected $_dao;

    const MODELO = 1;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_Orcamento();
        parent::__construct();
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(!isset($request['modelo'])){
            $object->modelo = 0;
        }
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->nome)){
            App_Validate_MessageBroker::addErrorMessage("O campo nome do orçamento é obrigatório.");
            return false;
        }

        if(empty($object->id_tp_orcamento)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um tipo de orçamento.");
            return false;
        }
        if(empty($object->id_empresa_cliente)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um cliente.");
            return false;
        }
        return true;
    }

}