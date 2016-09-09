<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/09/2013
 */
class Material_Model_Bo_Atributo extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Atributo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Atributo();
        parent::__construct();

        $this->_hasWorkspace                 = true;
        $this->_getRegistersWithoutWorkspace = true;
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->nome)){
            App_Validate_MessageBroker::addErrorMessage("O campo nome é obrigatório.");
            return false;
        }
        $criteria = array('nome = ?' => $object->nome, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        if(!empty($object->id_atributo)){
            $criteria = $criteria+array('id_atributo <> ?' => $object->id_atributo);
        }
        if(count($this->find($criteria))){
            App_Validate_MessageBroker::addErrorMessage('Já existe atributo com o nome '.$object->nome);
            return false;
        }
        return true;
    }

}