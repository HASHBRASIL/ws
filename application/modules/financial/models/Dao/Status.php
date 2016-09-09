<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  12/06/2013
 */
class Financial_Model_Dao_Status extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_status_financeiro";
    protected $_primary       = "stf_id";
    protected $_namePairs	  = "stf_descricao";

    protected $_rowClass = 'Financial_Model_Vo_Status';

    protected $_dependentTables = array('Financial_Model_Dao_Financial');

}

