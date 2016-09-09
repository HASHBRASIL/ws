<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 28/6/2013
 */
class Service_Model_Bo_ValorServico extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_ValorServico
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_ValorServico();
        parent::__construct();
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->vl_unitario = $this->_formatDecimal($object->vl_unitario);
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->vl_unitario)){
            App_Validate_MessageBroker::addErrorMessage('Preencha o campo valor unitário.');
            return false;
        }
        return true;
    }
}