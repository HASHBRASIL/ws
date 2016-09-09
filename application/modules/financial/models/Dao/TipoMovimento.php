<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Dao_TipoMovimento extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_tipo_movimento";
    protected $_primary       = "tmv_id";
    protected $_namePairs	  = "tmv_descricao";

    protected $_rowClass = 'Financial_Model_Vo_TipoMovimento';

    protected $_dependentTables = array('Financial_Model_Dao_Financial');

}

