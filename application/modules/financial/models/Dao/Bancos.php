<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  04/07/2013
 */
class Financial_Model_Dao_Bancos extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_bancos";
    protected $_primary       = "bco_id";
    protected $_namePairs	  = "bco_nome";

    protected $_rowClass = 'Financial_Model_Vo_Bancos';

     protected $_dependentTables = array('Financial_Model_Dao_Contas');

    public function __construct($config = array())
    {
        $this->_namePairs = new Zend_Db_Expr("bco_comp || ' - ' || bco_nome");
        parent::__construct($config);
    }
}

