<?php
/**
 * @author Vinícius S P Leônidas
 * @since  11/10/2013
 */
class Sis_Model_Dao_TipoFeedback extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_sis_tipo_feed';
	protected $_primary = 'id_tipo_feed';
	
	protected $_dependentTables = array('Sis_Model_Dao_Feedback');
}