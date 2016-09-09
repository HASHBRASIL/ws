<?php
class Service_Model_Dao_CentroCusto extends App_Model_Dao_Abstract
{
    protected $_name      = "fin_tb_centro_custo";
    protected $_primary   = 'cec_id';
    protected $_namePairs = "cec_descricao";

    protected $_rowClass = 'Service_Model_Vo_CentroCusto';

    protected $_referenceMap    = array(
            'Pai' => array(
                    'columns'           => 'cec_id_pai',
                    'refTableClass'     => 'Service_Model_Dao_CentroCusto',
                    'refColumns'        => 'cec_id'
            )
    );

}