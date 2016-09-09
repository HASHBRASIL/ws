<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  21/05/2013
 */
class Sis_Model_Bo_TipoControle extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_TipoControle
     */
    protected $_dao;

    const FORNECEDOR  = 1;
    const RECEPTOR    = 2;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_TipoControle();
        parent::__construct();
    }
}