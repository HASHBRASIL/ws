<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  12/08/2013
 */
class Processo_Model_Dao_MaterialProcesso extends App_Model_Dao_Abstract
{
    protected $_name     = "pro_tb_gp_material_processo";
    protected $_primary  = 'id_material_processo';

    protected $_rowClass = 'Processo_Model_Vo_MaterialProcesso';

    protected $_referenceMap    = array(
            'Unidade' => array(
                    'columns'           => 'id_tipo_unidade',
                    'refTableClass'     => 'Sis_Model_Dao_TipoUnidade',
                    'refColumns'        => 'id_tipo_unidade'
            ),
            'Marca' => array(
                    'columns'           => 'id_marca',
                    'refTableClass'     => 'Material_Model_Dao_Marca',
                    'refColumns'        => 'id_marca'
            ),
            'Item' => array(
                    'columns'           => 'id_item',
                    'refTableClass'     => 'Material_Model_Dao_Item',
                    'refColumns'        => 'id_item'
            ),
            'tipo Material' => array(
                    'columns'           => 'id_tp_material',
                    'refTableClass'     => 'Processo_Model_Dao_TipoMaterial',
                    'refColumns'        => 'id_tp_material'
            ),
            'Status' => array(
                    'columns'           => 'id_status_material',
                    'refTableClass'     => 'Processo_Model_Dao_StatusMaterial',
                    'refColumns'        => 'id_status_material'
            ),
            'Processo' => array(
                    'columns'           => 'id_processo',
                    'refTableClass'     => 'Processo_Model_Dao_Processo',
                    'refColumns'        => 'pro_id'
            )

    );
}