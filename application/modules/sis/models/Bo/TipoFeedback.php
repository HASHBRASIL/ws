<?php
/**
 * @author Vinícius S P Leônidas
 * @since  11/10/2013
 */
class Sis_Model_Bo_TipoFeedback extends App_Model_Bo_Abstract
{
	/**
	 * @var Sis_Model_Dao_TipoFeedback
	 */
	protected $_dao;
	
	public function __construct()
	{
		$this->_dao =  new Sis_Model_Dao_TipoFeedback();
    parent::__construct();
	}
	
}
