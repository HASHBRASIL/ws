<?php
class Resource_Model_Dao_Resource extends App_Model_Dao_Abstract
{

    protected $_name         = "tb_au_resource";
    protected $_primary      = "id_au_resource";
    protected $_namePairs	 = "name_resource";
    
    protected $_dependentTables = array('Auth_Model_Dao_Menu');

}