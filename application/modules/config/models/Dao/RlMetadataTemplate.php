<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_RlMetadataTemplate extends App_Model_Dao_Abstract
{
	protected $_name          = "rl_metadata_template";
	protected $_primary       = "id";
	protected $_namePairs	  = "id_metadata";
	
	protected $_rowClass = 'Config_Model_Vo_RlMetadataTemplate';
	
	protected $_dependentTables = array('Config_Model_Dao_Metadata');
		
}