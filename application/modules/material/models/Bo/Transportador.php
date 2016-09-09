<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/05/2013
 */
class Material_Model_Bo_Transportador extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Transportador
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Transportador();
        parent::__construct();
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->quantidade     = $this->_formatDecimal($object->quantidade);
        $object->peso_bruto     = $this->_formatDecimal($object->peso_bruto);
        $object->peso_liquido   = $this->_formatDecimal($object->peso_liquido);
    }
}