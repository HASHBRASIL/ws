<?php
/**
 * @author Vinicius Leônidas
 * @since 17/12/2013
 */
class Rh_Model_Bo_Funcionario extends App_Model_Bo_Abstract{
	
	/**
	 * @var Rh_Model_Dao_Funcionario
	 */
	protected $_dao;
	
	public function __construct(){
		$this->_dao = new Rh_Model_Dao_Funcionario();
		parent::__construct();
	}
	protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		$validator = new Zend_Validate_Date(array('format'=>'dd/MM/yyyy'));
		if(!$validator->isValid($object->dt_nascimento)){
			$object->dt_nascimento = null;
			App_Validate_MessageBroker::addErrorMessage("A data de emissão está incorreta.");
			return false;
		}
		if (!empty($object->id_rh_funcionario)) {
			$criteria = array('id_empresa = ?' => $object->id_empresa, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_rh_funcionario <> ?' => $object->id_rh_funcionario);
		} else {
			$criteria = array('id_empresa = ?' => $object->id_empresa, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
		}
		
		$fun = $this->find($criteria)->current();
		if(count($fun) > 0){
			App_Validate_MessageBroker::addErrorMessage("Este usuário já se encontra cadastrado.");
			return false;
		}
		return true;
	}
	
	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		$dt_nascimento = new Zend_Date($request['dt_nascimento']);
		$object->dt_nascimento = $dt_nascimento->toString('yyyy-MM-dd');
		
		if(!empty($object->dt_demissao)){
			$object->dt_demissao = $this->dateYmd($object->dt_demissao);
		}
		
	}
	protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		
		$documentoBo = new Rh_Model_Bo_DocumentoIdentidade();
		$certidaoBo = new Rh_Model_Bo_CertidaoCivil();
		$servicoMilitarBo = new Rh_Model_Bo_ServicoMilitar();
		$fgtsBo = new Rh_Model_Bo_Fgts();
		$ciBo = new Rh_Model_Bo_Ci();
		$outroBo = new Rh_Model_Bo_Outro();
		$admissaoBo = new Rh_Model_Bo_Admissao();
		$funcionaisBo = new Rh_Model_Bo_DadosFuncionais();
		
		$request['id_rh_funcionario'] = $object->id_rh_funcionario;
		
		$documentoObj = $documentoBo->get($request['id_rh_documento_identidade']);
		$documentoBo->saveFromRequest($request, $documentoObj);

		$certidaoObj = $certidaoBo->get($request['id_rh_certidao_civil']);
		$certidaoBo->saveFromRequest($request, $certidaoObj);
		
		$servicoMilitarObj = $servicoMilitarBo->get($request['id_rh_servico_militar']);
 		$servicoMilitarBo->saveFromRequest($request, $servicoMilitarObj);
 		
 		$fgtsObj = $fgtsBo->get($request['id_rh_fgts']);
 		$fgtsBo->saveFromRequest($request, $fgtsObj);
 		
		$ciObj = $ciBo->get($request['id_rh_ci']);
 		$ciBo->saveFromRequest($request, $ciObj);
 		
 		$outroObj = $outroBo->get($request['id_rh_outro']);
 		$outroBo->saveFromRequest($request, $outroObj);

 		$admissaoObj = $admissaoBo->get($request['id_rh_admissao']);
 		$admissaoBo->saveFromRequest($request, $admissaoObj);

 		$funcionaisObj = $funcionaisBo->get($request['id_rh_dados_funcionais']);
 		$funcionaisBo->saveFromRequest($request, $funcionaisObj);
		
	}
	
	public function getFun($id){
		
		$fun = $this->find(array('id_empresa = ?' => $id, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO))->current();
		if(!empty($fun)){
			return $resposta [] = array('ok' => true, 'id_rh_funcionario' => $fun->id_rh_funcionario);
		}
		return $resposta [] = array('ok' => false);
	} 

	public function getFuncionario($where){
	
		return $this->_dao->getFuncionario($where);
	
	}
	
	public function getIdFuncionario($where, $order = 'te.nome_razao'){
	
		return $this->_dao->getIdFuncionario($where, $order);
		
	}
}