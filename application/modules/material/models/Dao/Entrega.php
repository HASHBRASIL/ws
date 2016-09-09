<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/05/2013
 */
class Material_Model_Dao_Entrega extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_entrega";
    protected $_primary  = "id_entrega";

    protected $_rowClass = 'Material_Model_Vo_Entrega';

    protected $_referenceMap    = array(
            'Status' => array(
                    'columns'           => 'id_status',
                    'refTableClass'     => 'Material_Model_Dao_Status',
                    'refColumns'        => 'id_status'
            )
    );

}