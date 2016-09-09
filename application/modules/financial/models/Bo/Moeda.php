<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Bo_Moeda extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_Moeda
     */
    protected $_dao;

    const REAL  = 1;
    const DOLAR = 2;
    const EURO  = 3;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_Moeda();
        parent::__construct();
    }


}