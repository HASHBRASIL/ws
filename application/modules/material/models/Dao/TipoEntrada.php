<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Material_Model_Dao_TipoEntrada extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_tp_protocolo";
    protected $_primary  = "id_tp_protocolo";

    protected $_rowClass = 'Material_Model_Vo_TipoEntrada';

    protected $_dependentTables = array('Material_Model_Dao_Protocolo');

    protected $_referenceMap    = array(
            'Movimento' => array(
                    'columns'           => 'id_tp_movimento',
                    'refTableClass'     => 'Material_Model_Dao_TipoMovimento',
                    'refColumns'        => 'id_tp_movimento'
            )
    );
}