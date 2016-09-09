<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_Model_Dao_Classe extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_classe";
    protected $_primary  = "id_classe";

    protected $_rowClass = 'Material_Model_Vo_Classe';
    protected $_dependentTables = array('Material_Model_Dao_Item');

    protected $_referenceMap    = array(
            'Subgrupo' => array(
                    'columns'           => 'id_subgrupo',
                    'refTableClass'     => 'Material_Model_Dao_Subgrupo',
                    'refColumns'        => 'id_subgrupo'
            )
    );

}