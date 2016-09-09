<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_TibMetadata extends App_Model_Bo_Abstract
{
	/**
	 * @var Config_Model_Dao_TibMetadata
	 */
	protected $_dao;
	
	/**
	 * @var integer
	 */
	public function __construct()
	{
		$this->_dao = new Config_Model_Dao_TibMetadata();
		parent::__construct();
	}
	
}