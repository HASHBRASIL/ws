<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/12/2013
 */
class Processo_Model_Bo_Planejamento extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_Planejamento
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_Planejamento();
        parent::__construct();
    }


    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $date = new Zend_Date($object->data);
        $object->data = $date->toString('yyyy/MM/dd');
    }


}