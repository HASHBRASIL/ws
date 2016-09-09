<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  14/06/2013
 */
class Financial_Model_Dao_GrupoContas extends App_Model_Dao_Abstract
{
    protected $_name          = "fin_tb_grupo_contas";
    protected $_primary       = "grc_id";
    protected $_namePairs	  = "grc_descricao";

    protected $_rowClass = 'Financial_Model_Vo_GrupoContas';

    protected $_dependentTables = array('Financial_Model_Dao_PlanoContas');

}

