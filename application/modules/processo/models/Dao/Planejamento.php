<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/12/2013
 */
class Processo_Model_Dao_Planejamento extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_tb_gp_planejamento";
    protected $_primary  = "id_planejamento";

    protected $_rowClass = 'Processo_Model_Vo_Planejamento';


    protected $_referenceMap    = array(
            'Processo' => array(
                    'columns'           => 'id_processo',
                    'refTableClass'     => 'Processo_Model_Dao_Processo',
                    'refColumns'        => 'pro_id'
            ),
            'Prioridade' => array(
                    'columns'           => 'id_prioridade',
                    'refTableClass'     => 'Processo_Model_Dao_Prioridade',
                    'refColumns'        => 'id_prioridade'
            )
    );
}