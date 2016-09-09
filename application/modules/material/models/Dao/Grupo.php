<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_Model_Dao_Grupo extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_grupo";
    protected $_primary  = "id_grupo";

    protected $_rowClass = 'Material_Model_Vo_Grupo';
    protected $_dependentTables = array('Material_Model_Dao_Subgrupo', 'Material_Model_Dao_Item');
}