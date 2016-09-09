<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/07/2013
 */
class Sis_Model_Bo_Contato extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Contato
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Contato();
        parent::__construct();
    }

    protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        if($object->aniversario){
            $date = new Zend_Date($object->aniversario);
            $object->aniversario = $date->toString(Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY);
        }
        $object->telefone1 = str_replace(array('.', '-', '/', '(', ')', ' '), '', $object->telefone1);
        $object->telefone2 = str_replace(array('.', '-', '/', '(', ')', ' '), '', $object->telefone2);
        $object->telefone3 = str_replace(array('.', '-', '/', '(', ')', ' '), '', $object->telefone3);
    }


}