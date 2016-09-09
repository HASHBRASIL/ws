<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Material_Model_Bo_TipoEntrada extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_TipoEntrada
     */
    protected $_dao;

    const INTERNO = 1;
    const EXTERNO = 2;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_TipoEntrada();
        parent::__construct();
    }
}