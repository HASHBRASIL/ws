<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  12/08/2013
 */
class Processo_Model_Dao_ProcessoServico extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_tb_gp_processo_servico";
    protected $_primary  = 'id_processo_servico';

    protected $_rowClass = 'Processo_Model_Vo_ProcessoServico';

    protected $_referenceMap    = array(
            'Processo' => array(
                    'columns'           => 'id_processo',
                    'refTableClass'     => 'Processo_Model_Dao_Processo',
                    'refColumns'        => 'pro_id'
            ),
            'Servico' => array(
                    'columns'           => 'id_servico',
                    'refTableClass'     => 'Service_Model_Dao_Servico',
                    'refColumns'        => 'id_servico'
            )
    );
}