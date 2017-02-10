<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_MetadataTemplate extends App_Model_Dao_Abstract
{
	protected $_name          = "tp_metadata_template";
	protected $_primary       = "id";
	protected $_namePairs	  = "nome";
	
	protected $_rowClass = 'Config_Model_Vo_MetadataTemplate';
	
	protected $_dependentTables = array('Config_Model_Dao_Metadata');
		
}