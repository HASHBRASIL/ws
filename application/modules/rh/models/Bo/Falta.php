<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 25/08/2014
 */
class Rh_Model_Bo_Falta extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_Falta
	 */
	protected $_dao;
	
	public function __construct()
	{
		$this->_dao = new Rh_Model_Dao_Falta();
		parent::__construct();
	}
	
	public function getFaltaList($idFuncionario, $dtInicial, $dtFim)
	{
		$calculoPontoBo = new Rh_Model_Bo_CalculoPonto();
		
		$criteria = array(
					"data BETWEEN '{$dtInicial}' and '{$dtFim}'" => '',
					'hora_falta is not null' => '',
					"hora_falta <> ?" => '00:00:00'
			);
		return $calculoPontoBo->find($criteria);
	}
	
	public function saveByCalculoPonto($calculoPonto)
	{
		$configExtraBo = new Rh_Model_Bo_ConfigExtra();
		$faltaRow = $this->get();
		$faltaRow->id_calculo_ponto = $calculoPonto->id_calculo_ponto;
		$faltaRow->banco_horas = 1;
		$faltaRow->hora = $calculoPonto->hora_falta;
		if($this->validarWeekDsr($calculoPonto->data)){
			$faltaRow->dsr = true;
		}
		$this->insertdateAndCriacao($faltaRow);
		$faltaRow->save();
		return true;
	}
	
	public function validarWeekDsr($data)
	{
		$data = new Zend_Date($data);
		$falta = $this->_dao->validarWeekDsr($data->toString(Zend_Date::WEEK));
		if(count($falta) > 0){
			return false;
		}
		return true;
	}
	
	public function insertdateAndCriacao($object)
	{
		//verifica se possuir o campo e se possuir verifica se é criação ou atualização
		if(isset($object->id_criacao_usuario)){
			if(empty($object->id_criacao_usuario)){
				$object->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
			}elseif(isset($object->id_atualizacao_usuario)){
				$object->id_atualizacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
			}
		}
		 
		//verifica se possuir o campo e se possuir verifica se é criação ou atualização
		if(isset($object->dt_criacao)){
			if(empty($object->dt_criacao)){
				$object->dt_criacao = date('Y-m-d H:i:s');
			}elseif(isset($object->dt_atualizacao)){
				$object->dt_atualizacao = date('Y-m-d H:i:s');
			}
		}
	}
	
	public function sumHoraFalta($idCalculoPonto)
	{
		return $this->_dao->sumHoraFalta($idCalculoPonto);
	}

	protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if(isset($request['dsr']) && $request['dsr'] == 1){
			$data = new Zend_Date($request['data']);
			$faltaList = $this->_dao->validarWeekDsr($data->toString(Zend_Date::WEEK));
			if(count($faltaList) > 0){
				foreach ($faltaList as $falta){
					if($falta['id_falta'] == $object->id_falta)
						continue;
					
					$faltaObject = $this->get($falta['id_falta']);
					$faltaObject->dsr = 0;
					$faltaObject->save();
				}
			}
		}
	}
	
	public function deleteByCalculoPonto($idCalculoPonto)
	{
		return $this->_dao->delete(array('id_calculo_ponto = ?' => $idCalculoPonto));
	}
}