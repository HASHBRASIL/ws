<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/05/2013
 */
class Material_Model_Bo_Imposto extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Imposto
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Imposto();
        parent::__construct();
    }

    public function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
    {
        $object->bs_calc_icms       = $this->_formatDecimal($object->bs_calc_icms);
        $object->vl_icms            = $this->_formatDecimal($object->vl_icms);
        $object->bs_calc_icms_subst = $this->_formatDecimal($object->bs_calc_icms_subst);
        $object->vl_icms_subst      = $this->_formatDecimal($object->vl_icms_subst);
        $object->vl_frete           = $this->_formatDecimal($object->vl_frete);
        $object->vl_seguro          = $this->_formatDecimal($object->vl_seguro);
        $object->desconto           = $this->_formatDecimal($object->desconto);
        $object->vl_despesa_extra   = $this->_formatDecimal($object->vl_despesa_extra);
        $object->vl_ipi             = $this->_formatDecimal($object->vl_ipi);
        $object->tl_produto         = $this->_formatDecimal($object->tl_produto);
        $object->tl_nota            = $this->_formatDecimal($object->tl_nota);
    }
}