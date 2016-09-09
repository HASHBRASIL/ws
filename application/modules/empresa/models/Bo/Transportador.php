<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_Model_Bo_Transportador extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Bo_Transportador
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_Transportador();
        parent::__construct();
    }

}