<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_RlMetadataTemplate extends App_Model_Bo_Abstract
{
	/**
	 * @var Config_Model_Dao_RlMetadataTemplate
	 */
	protected $_dao;
	
	/**
	 * @var integer
	 */
	public function __construct()
	{
		$this->_dao = new Config_Model_Dao_RlMetadataTemplate();
		parent::__construct();
	}
}