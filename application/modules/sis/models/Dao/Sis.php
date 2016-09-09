<?php
/**
 * @author Carlos Vinicius Bonfim da Silva
 * @since  20/08/2013
 */
class Sis_Model_Dao_Sis extends App_Model_Dao_Abstract
{
    protected $_name = "tb_sis_proprietario";
    protected $_primary = "id_proprietario";
    protected $_rowClass	  = "Sis_Model_Vo_Sis";
    
    protected $_referenceMap    = array(
    
    		'Workspace' => array(
    				'columns'           => 'id_workspace',
    				'refTableClass'     => 'Auth_Model_Dao_Workspace',
    				'refColumns'        => 'id_workspace'
    		)
    	);

}