<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 18/08/2014
 */
class Rh_Model_Bo_ConfigExtra extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_ConfigExtra
	 */
	protected $_dao;
	
	const TRABALHADO = 1;
	const FOLGA		 = 2;
	const FERIADO	 = 3;
	
	public function __construct()
	{
		$this->_dao = new Rh_Model_Dao_ConfigExtra();
		parent::__construct();
	}
	
	public function getListExtra($idHorario = null)
	{
		$horarioList = array();
		if($idHorario){
			$criteria = array(
					'id_horario = ?' => $idHorario
			);
		}
		for($i = 1; $i <= 3; $i++){
			$criteria['tipo_dia = ?'] = $i;
			$config = null;
			if($idHorario){
				$config = $this->find($criteria, 'hora_inicio');
			}
			$extraList[] = array(
					'tipo_dia' 	=> $i,
					'name_tipo' => $this->getNameTipo($i),
					'config'	=> $config
			);
		}
	
		return $extraList;
	}
	
	private function getNameTipo($idTipo)
	{
		switch ($idTipo) {
			case self::TRABALHADO:
				return 'Trabalhado';
				break;
			case self::FOLGA:
				return 'Folga';
				break;
			case self::FERIADO:
				return 'Feriado';
				break;
		}
	}

	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if(!empty($object->porcentagem_desconto)){
			$object->banco_horas = 0;
		}
	}
	
	public function getExtraFuncionario($idHorario, $horaExtra, $data)
	{
		$feriadoBo 			= new Rh_Model_Bo_Feriado();
		$configHorarioBo 	= new Rh_Model_Bo_ConfigHorario();
		$tipoDia 			= self::TRABALHADO;

		if($configHorarioBo->hasFolga($idHorario, $data)){
			$tipoDia = self::FOLGA;
		}
		if($feriadoBo->hasFeriado($data)){
			$tipoDia = self::FERIADO;
		}
		
		$criteria = array('id_horario = ?' => $idHorario, 'hora_inicio < ?' => $horaExtra, 'tipo_dia = ?' => $tipoDia);
		return $this->find($criteria);
	}
	
}
