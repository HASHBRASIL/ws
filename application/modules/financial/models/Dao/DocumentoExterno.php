<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  17/06/2013
 */
class Financial_Model_Dao_DocumentoExterno extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_tipo_documento_externo";
    protected $_primary       = "tie_id";
    protected $_namePairs	  = "tie_descricao";

    protected $_rowClass = 'Financial_Model_Vo_DocumentoExterno';

     protected $_dependentTables = array('Financial_Model_Dao_Financial');

}

