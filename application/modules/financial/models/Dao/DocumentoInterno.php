<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Dao_DocumentoInterno extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_tipo_documento";
    protected $_primary       = "tid_id";
    protected $_namePairs	  = "tid_descricao";

    protected $_rowClass = 'Financial_Model_Vo_DocumentoInterno';

     protected $_dependentTables = array('Financial_Model_Dao_Financial');

}

