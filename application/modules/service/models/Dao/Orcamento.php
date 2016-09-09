<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 01/07/2013
 */
class Service_Model_Dao_Orcamento extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gs_orcamento";
    protected $_primary  = "id_orcamento";

    protected $_rowClass = 'Service_Model_Vo_Orcamento';

    protected $_referenceMap    = array(
            'Tipo Orcamento' => array(
                    'columns'           => 'id_tp_orcamento',
                    'refTableClass'     => 'Service_Model_Dao_TipoOrcamento',
                    'refColumns'        => 'id_tp_orcamento'
            ),
            'Cliente' => array(
                    'columns'           => 'id_empresa_cliente',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )
    );
}