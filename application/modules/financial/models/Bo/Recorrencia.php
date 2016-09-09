<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Bo_Recorrencia extends App_Model_Bo_Abstract
{
    /**
     * @var Financial_Model_Dao_Recorrencia
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Financial_Model_Dao_Recorrencia();
        parent::__construct();
    }
    
    
}