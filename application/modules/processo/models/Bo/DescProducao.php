<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Processo_Model_Bo_DescProducao extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_DescProducao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_DescProducao();
        parent::__construct();
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->id_categoria)){
            App_Validate_MessageBroker::addErrorMessage("Selecione uma categoria.");
            return false;
        }
        return true;
    }

}