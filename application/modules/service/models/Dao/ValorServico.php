<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 28/06/2013
 */
class Service_Model_Dao_ValorServico extends App_Model_Dao_Abstract
{
    protected $_name         = "tb_gs_valor_servico";
    protected $_primary      = "id_valor_servico";
    protected $_namePairs    = "vl_unitario";

    protected $_rowClass = 'Service_Model_Vo_ValorServico';

    protected $_referenceMap    = array(
            'Servico' => array(
                    'columns'           => 'id_servico',
                    'refTableClass'     => 'Service_Model_Dao_Servico',
                    'refColumns'        => 'id_servico'
            ),
            'Empresa' => array(
                    'columns'           => 'id_empresa',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )
    );
}