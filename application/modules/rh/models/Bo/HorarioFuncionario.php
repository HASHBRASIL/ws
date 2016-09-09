<?php
/**
 * @author Ellyson de Jesus Silva
* @since 29/07/2014
*/
class Rh_Model_Bo_HorarioFuncionario extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_HorarioFuncionario
	 */
	protected $_dao;

	public function __construct()
	{
		$this->_dao = new Rh_Model_Dao_HorarioFuncionario();
		parent::__construct();
	}
	

	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		$object->data = $this->dateYmd($object->data);
	}
	
	public function getHorarioPadrao($dtInicio, $dtFinal, $idFuncionario = null, $idHorario = null)
	{
		$criteria = array();
		if($idFuncionario){
			$criteria['id_rh_funcionario = ?'] = $idFuncionario; 
		}
		if($idHorario){
			$criteria['id_horario = ?'] = $idHorario; 
		}
		$horarioPadrao = $this->find(array('data BETWEEN "'.$dtInicio.'" and "'.$dtFinal.'"' => '')+$criteria)->current();
		if(empty($horarioPadrao)){
			$horarioPadrao = $this->find(array('data < ?'=> $dtInicio)+$criteria, 'data DESC')->current();
		}else{
			$dtInicio = new Zend_Date($dtInicio);
			$data	  = new Zend_Date($horarioPadrao->data);
			if($dtInicio->isEarlier($data)){
				$horarioPadraoArray   = !empty($horarioPadrao) ? array($horarioPadrao): array();
				$horario = $this->find(array('data < ?' => $dtInicio->toString('yyyy-MM-dd'))+$criteria, 'data DESC')->current();
				if(!empty($horario)){
					$horarioPadraoArray[] = $horario;
				}
				$horarioPadrao = $horarioPadraoArray;
			}
		}
		return $horarioPadrao;
	}
}