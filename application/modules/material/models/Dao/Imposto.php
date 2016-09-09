<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/05/2013
 */
class Material_Model_Dao_Imposto extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_imposto";
    protected $_primary  = "id_imposto";

    protected $_dependentTables = array('Material_Model_Dao_Nfe');

}