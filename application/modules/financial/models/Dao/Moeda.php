<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Dao_Moeda extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_moedas";
    protected $_primary       = "moe_id";
    protected $_namePairs	  = "moe_descricao";

    protected $_rowClass = 'Financial_Model_Vo_Moeda';

     protected $_dependentTables = array('Financial_Model_Dao_Financial');

}

