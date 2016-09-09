<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  12/06/2013
 */
class Financial_Model_Bo_Status extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_Status
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_Status();
        parent::__construct();
    }
    
    
}