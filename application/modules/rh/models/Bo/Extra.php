<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 21/08/2014
 */
class Rh_Model_Bo_Extra extends App_Model_Bo_Abstract
{
	/**
	 * @var Rh_Model_Dao_Extra
	 */
	protected $_dao;
	
	const APROVADO = 1;
	const REPROVADO = 0;
	
	public function __construct()
	{
		$this->_dao = new Rh_Model_Dao_Extra();
		parent::__construct();
	}
	
	public function getExtraList($idFuncionario, $dtInicial, $dtFim)
	{
		$calculoPontoBo = new Rh_Model_Bo_CalculoPonto();
		
		$criteria = array(
					"data BETWEEN '{$dtInicial}' and '{$dtFim}'" => '',
					'hora_extra is not null' => '',
					"hora_extra <> ?" => '00:00:00',
					'id_rh_funcionario = ?' => $idFuncionario
			);
		return $calculoPontoBo->find($criteria);
	}
	
	public function saveByCalculoPonto($calculoPonto)
    {
    	$configExtraBo = new Rh_Model_Bo_ConfigExtra();
    	if(empty($calculoPonto->id_horario)){
    		$extraRow = $this->get();
    		$extraRow->id_calculo_ponto = $calculoPonto->id_calculo_ponto;
    		$extraRow->banco_horas = 1;
    		$extraRow->hora = $calculoPonto->hora_extra;
    		$this->insertdateAndCriacao($extraRow);
    		$extraRow->save();
    		return true;
    	}
    	$configExtra = $configExtraBo->getExtraFuncionario($calculoPonto->id_horario, $calculoPonto->hora_extra, $calculoPonto->data);
    	$horaExtra = new Zend_Date($calculoPonto->hora_extra, 'HH:mm:ss');
		
    	foreach ($configExtra as $config){
    		$extraRow = $this->get();
    		$extraRow->id_calculo_ponto = $calculoPonto->id_calculo_ponto;
    		$extraRow->porcentagem = $config->porcentagem_desconto;
    		$extraRow->banco_horas = $config->banco_horas;
    		$extraRow->hora = $horaExtra->toString('HH:mm:ss');
    		$configHoraFim = new Zend_Date($config->hora_fim, 'HH:mm:ss');
    		if($configHoraFim->isEarlier($horaExtra)){
    			$extraRow->hora = $config->hora_fim;
    			$horaExtra->sub($configHoraFim, 'HH');
    			$horaExtra->sub($configHoraFim, 'mm');
    		}
    		$this->insertdateAndCriacao($extraRow);
    		$extraRow->save();
    	}
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
    
    public function sumHoraExtra($idCalculoPonto)
    {
    	return $this->_dao->sumHoraExtra($idCalculoPonto);
    }
    

    public function aprovarGerente($object)
    {
    	if(!$this->validarGerente($object)){
    		throw new App_Validate_Exception();
    	}
    	$object->id_aprovacao_gerente = Zend_Auth::getInstance()->getIdentity()->usu_id;
    	$this->insertdateAndCriacao($object);
    	$object->save();
    }
    
    private function validarGerente($object)
    {
    	$configuracaoBo = new Rh_Model_Bo_Configuracao();
    	if(!$configuracaoBo->hasAprovacao(Zend_Auth::getInstance()->getIdentity()->usu_id, Rh_Model_Bo_ConfiguracaoUsuario::NIVEL1)){
    		App_Validate_MessageBroker::addErrorMessage('Este usuário não possui permissão para aprovar.');
    		return false;
    	}
    	return true;
    }
    
    private function validarDiretor($object)
    {
    	$configuracaoBo = new Rh_Model_Bo_Configuracao();
    	if(!$configuracaoBo->hasAprovacao(Zend_Auth::getInstance()->getIdentity()->usu_id, Rh_Model_Bo_ConfiguracaoUsuario::NIVEL2)){
    		App_Validate_MessageBroker::addErrorMessage('Este usuário não possui permissão para aprovar.');
    		return false;
    	}
    	return true;
    }

    public function aprovarDiretor($object)
    {
    	if(!$this->validarDiretor($object)){
    		throw new App_Validate_Exception();
    	}
    	$object->aprovado = self::APROVADO;
    	$object->id_aprovacao_diretor = Zend_Auth::getInstance()->getIdentity()->usu_id;
    	if(empty($object->id_aprovacao_gerente)){
    		$object->id_aprovacao_gerente = Zend_Auth::getInstance()->getIdentity()->usu_id;
    	}
    	$this->insertdateAndCriacao($object);
    	$object->save();
    }
    
    public function reprovarGerente($object)
    {
    	if(!$this->validarGerente($object)){
    		throw new App_Validate_Exception();
    	}
    	$object->id_aprovacao_gerente = Zend_Auth::getInstance()->getIdentity()->usu_id;
    	$object->aprovado = self::REPROVADO;
    	$this->insertdateAndCriacao($object);
    	$object->save();
    }

    public function reprovarDiretor($object)
    {
    	if(!$this->validarDiretor($object)){
    		throw new App_Validate_Exception();
    	}
    	$object->id_aprovacao_diretor = Zend_Auth::getInstance()->getIdentity()->usu_id;
    	$object->aprovado = self::REPROVADO;
    	if(empty($object->id_aprovacao_gerente)){
    		$object->id_aprovacao_gerente = Zend_Auth::getInstance()->getIdentity()->usu_id;
    	}
    	$this->insertdateAndCriacao($object);
    	$object->save();
    }
    
    public function deleteByCalculoPonto($idCalculoPonto)
    {
    	return $this->_dao->delete(array('id_calculo_ponto = ?' => $idCalculoPonto));
    }
}
