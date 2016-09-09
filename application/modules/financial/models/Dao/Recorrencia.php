<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Dao_Recorrencia extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_recorrencia_fin";
    protected $_primary       = "rcf_id";
    protected $_namePairs	  = "rcf_descricao";

    protected $_rowClass = 'Financial_Model_Vo_Recorrencia';

     protected $_dependentTables = array('Financial_Model_Dao_Financial');

}

