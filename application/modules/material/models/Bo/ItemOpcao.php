<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/10/2013
 */
class Material_Model_Bo_ItemOpcao extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_ItemOpcao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_ItemOpcao();
        parent::__construct();
    }

    public function findOpcaoByItem($idItem)
    {
        $atributoList = $this->_dao->findAtributoByItem($idItem);
        foreach ($atributoList as $key => $atributo){
            $atributoList[$key]['opcao'] = $this->_dao->findOpcao($idItem, $atributo['id_atributo']);
        }
        return $atributoList;
    }

    public function saveFromRequest( $request, $object){
        $object = $object->setFromArray($request);
        //verifica se possuir o campo e se possuir verifica se é criação ou atualização
        if(isset($object->id_criacao_usuario)){
            if(empty($object->id_criacao_usuario)){
                $object->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
            }else if(isset($object->id_atualizacao_usuario)){
                $object->id_atualizacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
            }
        }

        //verifica se possuir o campo e se possuir verifica se é criação ou atualização
        if(isset($object->dt_criacao)){
            if(empty($object->dt_criacao)){
                $object->dt_criacao = date('Y-m-d H:i:s');
            }else if(isset($object->dt_atualizacao)){
                $object->dt_atualizacao = date('Y-m-d H:i:s');
            }
        }

        $uploadAdapter = new Zend_File_Transfer_Adapter_Http();

        $resultValidation = $this->_validar($object, $uploadAdapter);
        if(!$resultValidation){
            throw new App_Validate_Exception();
        }
        try {
            foreach ($request['opcao'] as $opcao){
                $object = $this->get();
                $object->id_opcao = $opcao;
                $object->id_item = $request['id_item'];
                $this->_preSave($object, $request, $uploadAdapter);
                $object->save();
                $this->_postSave($object, $request, $uploadAdapter);
            }
        } catch (Exception $e) {
            App_Validate_MessageBroker::addErrorMessage("O sistema se encontra fora do ar no momento. Caso persista entre em contato com o Administrador.".$e->getMessage());
            throw new App_Validate_Exception();
        }
    }

    protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $request = new Zend_Controller_Request_Http();
        $params  = $request->getParams();
        if(count($this->_dao->findOpcao($params['id_item'], $params['id_atributo']))){
            App_Validate_MessageBroker::addErrorMessage('Já existe este atributo para este produto.');
            return false;
        }
        if(!isset($params['opcao']) || count($params['opcao']) == 0){
            App_Validate_MessageBroker::addErrorMessage('Selecione uma opção.');
            return false;
        }
        return true;
    }

    public function deleteByAtributo($idAtributo, $idItem)
    {
        return $this->_dao->deleteByAtributo($idAtributo, $idItem);
    }

    public function findOpcao($idItem, $idAtributo = null)
    {
        return $this->_dao->findOpcao($idItem, $idAtributo);
    }

}