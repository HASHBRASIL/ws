<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  12/08/2013
 */
class Processo_Model_Bo_ProcessoServico extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_ProcessoServico
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_ProcessoServico();
        parent::__construct();
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->vl_unitario   = $this->_formatDecimal($object->vl_unitario);
        $object->total         = $this->_formatDecimal($object->total);
    }

}