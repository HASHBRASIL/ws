<?php
/**
 * @author Vinícius S P Leônidas
 * @since  11/10/2013
 */
class Sis_Model_Bo_Feedback extends App_Model_Bo_Abstract
{
  /**
  * @var Sis_Model_Dao_Feedback
  */
	protected $_dao;

	public function __construct()
	{
		$this->_dao =  new Sis_Model_Dao_Feedback();
		parent::__construct();
	}

	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
	    $objectVerificar = $this->get($object->id_feed);
		if (!empty($request['id_criacao_usuario'])) {
			$object->id_criacao_usuario = null;
		}
		if($object->id_criacao_usuario != $objectVerificar->id_criacao_usuario && !empty($object->id_feed)){
		    $object->id_criacao_usuario = $objectVerificar->id_criacao_usuario;
		}

		if (empty($object->id_status_feed)) {
		    $object->id_status_feed = Sis_Model_Bo_StatusFeedback::ABERTO;
		}
	}

}
