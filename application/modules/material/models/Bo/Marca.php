<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 10/04/2013
 */
class Material_Model_Bo_Marca extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Marca
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Marca();
        parent::__construct();
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->nome)){
            App_Validate_MessageBroker::addErrorMessage("O campo nome está vazio.");
            return false;
        }

        $criteria = array('nome = ?' => $object->nome);
        $marca = $this->find($criteria);
        if(count($marca)){
            App_Validate_MessageBroker::addErrorMessage('Já existe marca com este nome!');
            return false;
        }

        return true;
    }

}