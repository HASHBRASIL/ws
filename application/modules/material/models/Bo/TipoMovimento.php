<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  24/05/2013
 */
class Material_Model_Bo_TipoMovimento extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_TipoMovimento
     */
    protected $_dao;

    const ENTRADA = 1;
    const SAIDA   = 2;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_TipoMovimento();
        parent::__construct();
    }

}