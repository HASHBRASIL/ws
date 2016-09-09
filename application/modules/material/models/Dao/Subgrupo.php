<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_Model_Dao_Subgrupo extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_subgrupo";
    protected $_primary  = "id_subgrupo";

    protected $_rowClass = 'Material_Model_Vo_Subgrupo';
    protected $_dependentTables = array('Material_Model_Dao_Classe', 'Material_Model_Dao_Item');

    protected $_referenceMap    = array(
            'Grupo' => array(
                    'columns'           => 'id_grupo',
                    'refTableClass'     => 'Material_Model_Dao_Grupo',
                    'refColumns'        => 'id_grupo'
            )
    );
}