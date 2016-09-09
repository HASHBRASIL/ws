<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/05/2013
 */
class Material_Model_Dao_Transportador extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_transportador";
    protected $_primary  = "id_transportador";
    protected $_rowClass = 'Material_Model_Vo_Transportador';

    protected $_dependentTables = array('Material_Model_Dao_Nfe', 'Material_Model_Dao_Protocolo');

    protected $_referenceMap    = array(
            'Empresa' => array(
                    'columns'           => 'id_transp_empresa',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            )
    );
}