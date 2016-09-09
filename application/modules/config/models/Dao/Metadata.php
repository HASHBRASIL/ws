<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_Metadata extends App_Model_Dao_Abstract
{
	protected $_name          = "tp_metadata";
	protected $_primary       = "id";
	//protected $_namePairs	  = "nome";
	
	protected $_rowClass = 'Config_Model_Vo_Metadata';
	
	//protected $_dependentTables = array('Financial_Model_Dao_Financial');
}