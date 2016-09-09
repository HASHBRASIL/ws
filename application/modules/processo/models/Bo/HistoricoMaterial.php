<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  12/08/2013
 */
class Processo_Model_Bo_HistoricoMaterial extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_HistoricoMaterial
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_HistoricoMaterial();
        parent::__construct();
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
        $object->dt_criacao = date('Y-m-d H:i:s');
    }

}