<?php
class Sis_Model_Bo_TipoUnidade extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_TipoUnidade
     */
    protected $_dao;
    
    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_TipoUnidade();
        parent::__construct();
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->nome)){
            App_Validate_MessageBroker::addErrorMessage("O campo nome da unidade é obrigatório!");
            return false;
        }

        $criteria = array('nome = ?' => $object->nome);
        if(!empty($object->id_tipo_unidade)){
            $criteria = array(
                    'nome = ?'            => $object->nome,
                    'id_tipo_unidade = ?' => $object->id_tipo_unidade
            );
        }
        $unidade = $this->find($criteria);
        if(count($unidade)){
            App_Validate_MessageBroker::addErrorMessage("Já existe está unidade.");
            return false;
        }
        return true;
    }


}