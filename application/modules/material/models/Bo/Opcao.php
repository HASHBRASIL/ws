<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/09/2013
 */
class Material_Model_Bo_Opcao extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Opcao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Opcao();
        parent::__construct();
    }

    public function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if(empty($object->nome)){
            App_Validate_MessageBroker::addErrorMessage("O campo nome é obrigatório.");
            return false;
        }
        if(empty($object->id_atributo)){
            App_Validate_MessageBroker::addErrorMessage('Não possui atributo.' );
            return false;
        }
        $criteria = array('nome = ?' => $object->nome, 'id_atributo = ?' => $object->id_atributo, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        if(!empty($object->id_opcao)){
            $criteria = $criteria+array('id_opcao <> ?' => $object->id_opcao);
        }
        if(count($this->find($criteria))){
            App_Validate_MessageBroker::addErrorMessage('Já existe a opção deste atributo com o nome '.$object->nome);
            return false;
        }
        return true;
    }

    public function getPairsByAtributo($idAtributo, $ativo = true, $chave = null, $valor = null,
            $ordem = null, $limit = null )
    {
        $where = array('id_atributo = ?' => $idAtributo);
        if($ativo){
            $where = $where+array("ativo = ?" => App_Model_Dao_Abstract::ATIVO);
        }
        return $this->_dao->fetchPairs($chave, $valor, $where, $ordem, $limit);
    }

}