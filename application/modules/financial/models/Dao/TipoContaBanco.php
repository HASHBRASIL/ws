<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  04/07/2013
 */
class Financial_Model_Dao_TipoContaBanco extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_tipo_contabanco";
    protected $_primary       = "tcb_id";
    protected $_namePairs	  = "tcb_descricao";

    protected $_rowClass = 'Financial_Model_Vo_TipoContaBanco';

     protected $_dependentTables = array('Financial_Model_Dao_Contas');

}

