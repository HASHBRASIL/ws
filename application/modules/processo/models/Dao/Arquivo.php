<?php
/**
 * @author Carlos Vinicius Bonfim
 * @since  08/07/2013
 */
class Processo_Model_Dao_Arquivo extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_tb_gp_arquivo";
    protected $_primary  = "id_arquivo";

    protected $_rowClass = 'Processo_Model_Vo_Arquivo';

    protected $_referenceMap    = array(
    		'Arquivo' => array(
    				'columns'           => 'pro_id',
    				'refTableClass'     => 'Processo_Model_Dao_Processo',
    				'refColumns'        => 'pro_id'
    		));
}