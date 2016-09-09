<?php
/**
 * @author Ellyson de Jesus
* @since 05/08/2014
*/
class Rh_Model_Bo_CalculoPonto extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_CalculoPonto
	 */
	protected $_dao;

	public function __construct()
	{
		$this->_dao = new Rh_Model_Dao_CalculoPonto();
		parent::__construct();
	}
	
	public function calcularPonto(Rh_Model_Dao_FolhaPonto $ponto)
	{
		$horarioPadrao = $ponto->getHorarioPadrao();
		if(count($ponto->getTempo()) == 0 && !empty($horarioPadrao)){
			$calculoRow = $this->saveCalculo($ponto, null, $ponto->sumChDefault());
			return $calculoRow;
		}
		if(empty($horarioPadrao) || !empty($ponto->getFeriado())){
			$calculoRow = $this->saveCalculo($ponto, $ponto->sumChTrabalhado(), null);
			return $calculoRow;
		}
		if($horarioPadrao['compensado']){
			$sumChTrabalhado 	= DateTime::createFromFormat('H:i:s', $ponto->sumChTrabalhado());
			$sumChDefault 		= DateTime::createFromFormat('H:i:s', $ponto->sumChDefault());
			$diff 				= $sumChTrabalhado->diff($sumChDefault);
			$hora 				= DateTime::createFromFormat('H:i:s', $diff->format('%H:%I:%S'));
			
			if($sumChTrabalhado < $sumChDefault && ( $diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_falta'] ) ){
				$calculoRow = $this->saveCalculo($ponto, null, $hora->format('H:i:s'));
				return $calculoRow;
			}elseif ($sumChTrabalhado > $sumChDefault && ( $diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_extra'] ) ){
				$calculoRow = $this->saveCalculo($ponto, $hora->format('H:i:s'));
				return $calculoRow;
			}
		}else {
			$horaArray = $ponto->getOnlyTempo();
			$tempo = $ponto->getTempo();
			
			$hora_extra = DateTime::createFromFormat('H:i:s', '00:00:00');
			$hora_falta = DateTime::createFromFormat('H:i:s', '00:00:00');
			if(empty($horaArray['entrada1']) && empty($horaArray['saida1']) && !empty($horarioPadrao['entrada1']) && !empty($horarioPadrao['saida1'])){
				$horaTrabalhado 	= DateTime::createFromFormat('H:i:s', $horarioPadrao['entrada1']);
				$horaDefault 		= DateTime::createFromFormat('H:i:s', $horarioPadrao['saida1']);
				$diff 				= $horaTrabalhado->diff($horaDefault);
				$diff->invert 		= 0;
				$hora_falta->add($diff);
			}
			if(empty($horaArray['entrada2']) && empty($horaArray['saida2'])  && !empty($horarioPadrao['entrada2']) && !empty($horarioPadrao['saida2'])){
				$horaTrabalhado 	= DateTime::createFromFormat('H:i:s', $horarioPadrao['entrada2']);
				$horaDefault 		= DateTime::createFromFormat('H:i:s', $horarioPadrao['saida2']);
				$diff 				= $horaTrabalhado->diff($horaDefault);
				$diff->invert 		= 0;
				$hora_falta->add($diff);
			}
			
			foreach ($horaArray as $key => $hora ){
				if(empty($hora)){
					continue;
				}
				if(empty($horarioPadrao[$key])){
					continue;
				}

				$horaTrabalhado 	= DateTime::createFromFormat('H:i:s', $hora);
				$horaDefault 		= DateTime::createFromFormat('H:i:s', $horarioPadrao[$key]);
				$diff 				= $horaTrabalhado->diff($horaDefault);
				$hora 				= DateTime::createFromFormat('H:i:s', $diff->format('%H:%I:%S'));
				$diff->invert = 0;
				switch ($key) {
					case 'entrada1':
						if($horaTrabalhado > $horaDefault && ($diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_falta'] ) ){
							$hora_falta->add($diff);
						}elseif ($horaTrabalhado < $horaDefault && ( $diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_extra'] ) ){
							$hora_extra->add($diff);
						}
						break;
					case 'saida1':
						if(!$horarioPadrao['almoco_livre'] && $horaTrabalhado > $horaDefault && ($diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_extra'] ) ){
							$hora_extra->add($diff);
						}elseif (!$horarioPadrao['almoco_livre'] && $horaTrabalhado < $horaDefault && ($diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_falta'] ) ){
							$hora_falta->add($diff);
						}
						break;
					case 'entrada2':
						if(!$horarioPadrao['almoco_livre'] && $horaTrabalhado > $horaDefault && ($diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_falta'] ) ){
							$hora_falta->add($diff);
						}elseif (!$horarioPadrao['almoco_livre'] && $horaTrabalhado < $horaDefault && ($diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_extra'] ) ){
							$hora_extra->add($diff);
						}
						break;
					case 'saida2':
						if(!empty($horarioPadrao['fechamento'])){
							$dataFechamento = DateTime::createFromFormat('H:i:s', $horarioPadrao['fechamento']);
							$dataFimDia = DateTime::createFromFormat('H:i:s', '00:00:00');
							if($dataFimDia < $horaTrabalhado && $dataFechamento > $horaTrabalhado){
								$horaTrabalhado->modify('+1 day');
								$diff 				= $horaTrabalhado->diff($horaDefault);
								$diff->invert 		= 0;
							}
						}
						if($horaTrabalhado > $horaDefault && ($diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_extra'] ) ){
							$hora_extra->add($diff);
						}elseif ($horaTrabalhado < $horaDefault && ($diff->h > 0 || $diff->i > $horarioPadrao['tolerancia_falta'] ) ){
							$hora_falta->add($diff);
						}
						break;
				}
			}

			$calculoRow = $this->saveCalculo($ponto, $hora_extra->format('H:i:s'), $hora_falta->format('H:i:s'));
			return $calculoRow;
		}
	}
	
	public function getRow($criteria)
	{
		$row = $this->find($criteria)->current();
		if(count($row)>0){
			return $row;
		}
		return $this->get();
	}
	
	public function setDataCriacaoAndAtualizacao($object)
	{
		if(isset($object->id_criacao_usuario)){
			if(empty($object->id_criacao_usuario)){
				$object->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
			}else if(isset($object->id_atualizacao_usuario)){
				$object->id_atualizacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
			}
		}
		
		//verifica se possuir o campo e se possuir verifica se é criação ou atualização
		if(isset($object->dt_criacao)){
			if(empty($object->dt_criacao)){
				$object->dt_criacao = date('Y-m-d H:i:s');
			}else if(isset($object->dt_atualizacao)){
				$object->dt_atualizacao = date('Y-m-d H:i:s');
			}
		}
	}

	/**
	 * @desc Responsável por salvar o calculo
	 * @param Rh_Model_Dao_CalculoPonto $object
	 * @param Rh_Model_Dao_FolhaPonto $ponto
	 * @param Time $hora
	 */
	private function saveCalculo(Rh_Model_Dao_FolhaPonto $ponto, $horaExtra = null, $horaFalta = null)
	{
		$horarioPadrao = $ponto->getHorarioPadrao();
		$criteria = array(
				'id_rh_funcionario = ?' => $ponto->getIdFuncionario(),
				'data = ?' => $ponto->getData()
		);
		$object 					= $this->getRow($criteria);
		if(!empty($object->id_calculo_ponto) && $object->hora_extra != $horaExtra){
			$extraBo = new Rh_Model_Bo_Extra();
			$extraBo->deleteByCalculoPonto($object->id_calculo_ponto);
		}
		if(!empty($object->id_calculo_ponto) && $object->hora_falta != $horaFalta){
			$faltaBo = new Rh_Model_Bo_Falta();
			$faltaBo->deleteByCalculoPonto($object->id_calculo_ponto);
		}
		$object->id_rh_funcionario 	= $ponto->getIdFuncionario();
		$object->id_horario			= $horarioPadrao['id_horario'];
		$object->data 				= $ponto->getData();
		$object->hora_extra			= $horaExtra;
		$object->hora_falta 		= $horaFalta;

		$this->setDataCriacaoAndAtualizacao($object);
		$object->id_atualizacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
		try {
			$object->save();
		} catch (Exception $e) {
			App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro entre em contato com o Administrador. ".$e->getMessage());
		}
		return $object;
	}

}
