<?php
/**
 * @author Alexandre Nascimetnto Barbosa
 * @since  14/06/2013
 */
class Relatorio_Model_Dao_StatusFinanceiro extends App_Model_Dao_Abstract
{
    protected $_name = "tb_status_financeiro";
    protected $_primary = "stf_id";
    protected $_namePairs = 'stf_descricao';

    protected $_dependentTables = array('Relatorio_Model_Dao_Financeiro');


}