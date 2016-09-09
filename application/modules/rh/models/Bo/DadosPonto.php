<?php
/**
 * @author Vinicius Leônidas
 * @since 29/01/2014
 */
class Rh_Model_Bo_DadosPonto extends App_Model_Bo_Abstract
{
    /**
     * @var Rh_Model_Dao_DadosPonto
     */
	protected $_dao;

	const DUPLICADO          = 1;
	Const DUPLICADO_APROVADO = 3;
	const NAO_DUPLICADO      = 0;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_DadosPonto();
		parent::__construct();
	}
	
	public function folhaPonto($dataInicio, $dataFinal, $idFuncionario, $idPonto = null)
	{
		$data_inicial = new DateTime( implode( '-', array_reverse( explode( '/', $dataInicio ) ) ) );
		$data_final   = new DateTime( implode( '-', array_reverse( explode( '/', $dataFinal ) ) ) );
		$configHorarioBo	= new Rh_Model_Bo_ConfigHorario();
		$dadosFinal	  = array();
		
		while( $data_inicial <= $data_final ) {
			
			$folhaPonto 	= new Rh_Model_Dao_FolhaPonto($data_inicial->format( 'Y-m-d' ),$idFuncionario);
			$horarioPadrao  = $configHorarioBo->horarioPadraoByDia($data_inicial->format( 'Y-m-d' ), $idFuncionario);
			$dadosPonto 	= $this->_dao->listaPontoManual($idPonto,$data_inicial->format( 'Y-m-d' ), $idFuncionario, $horarioPadrao);
			$totalDeDados  	= count($dadosPonto);
		
			for ($i = 0; $i < $totalDeDados; $i++) {
				$folhaPonto->setTempo($dadosPonto[$i]['hora'], $dadosPonto[$i]['id_rh_dados_ponto'],$dadosPonto[$i]['descricao'], $dadosPonto[$i]['justificativa'], $dadosPonto[$i]['posicao']);
			}
		
			$folhaPonto->unsetDuplicado();
			$dadosFinal[$data_inicial->format('d/m/Y')] =  $folhaPonto;
			$dadosFinal[$data_inicial->format('d/m/Y')]->calcularPonto();
			$data_inicial->add( DateInterval::createFromDateString( '1 days' ) );
			
		}
		return $dadosFinal;
	}

	public function savePontoManual($param){

		$documentosBo = new Rh_Model_Bo_DocumentoIdentidade();

		if (!empty($param['pk'])) {
			$newPonto = $this->_dao->get($param['pk']);
		} else {
			$newPonto = $this->_dao->createRow();
		}

		$date = new Zend_Date($param['data']);
		$pis = $documentosBo->find(array('id_rh_funcionario = ?' => $param['fun']))->current();

		$this->_dao->getAdapter()->beginTransaction();
		try {

			if (!empty($param['value'])) {
				$newPonto->hora = $param['value'];
			}

			$newPonto->data = $date->toString("yyyy-MM-dd");
			$newPonto->pis = $pis['pis'];
			$newPonto->tipo = App_Model_Dao_Abstract::ATIVO;
			$newPonto->id_rh_justificacao_ponto = "";
			$newPonto->id_rh_registro_ponto = $param['ponto'];
			$newPonto->id_rh_funcionario = $param['fun'];
			$newPonto->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
			$newPonto->dt_criacao = Zend_Date::now()->toString("yyyy-MM-dd h:m:s");
			$newPonto->save();

			$this->_dao->getAdapter()->commit();
		} catch (Exception $e) {

			$this->_dao->getAdapter()->rollBack();
		}

		return $newPonto;
	}

	public function saveMotivoPontoManual($param){

		$documentosBo = new Rh_Model_Bo_DocumentoIdentidade();

		$this->_dao->getAdapter()->beginTransaction();
		try {

			$newPonto = $this->_dao->get($param['id_rh_dados_ponto']);

			if ($param['id_rh_justificacao_ponto'] != "") {
				$newPonto->hora = "";
			}
			$newPonto->descricao = $param['descricao'];
			$newPonto->id_rh_justificacao_ponto= $param['id_rh_justificacao_ponto'];
			$newPonto->save();

			$this->_dao->getAdapter()->commit();
		} catch (Exception $e) {

			$this->_dao->getAdapter()->rollBack();
		}

		return $newPonto;
	}

	public function updateIdFuncionario($pis, $idFuncionario){

		$todosPisFuncionario = $this->find(array('pis = ?' => $pis));

		if(count($todosPisFuncionario) != 0){
			foreach ($todosPisFuncionario as $dados){

				$updatePonto = $this->get($dados['id_rh_dados_ponto']);
				$updatePonto->id_rh_funcionario = $idFuncionario;
				$updatePonto->save();

			}
		}

	}

	public function migrarTxt($idPonto){
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		$documentosBo = new Rh_Model_Bo_DocumentoIdentidade();
		$pontoBo = new Rh_Model_Bo_Ponto();
		$pontoObj = $pontoBo->get($idPonto);
		$arquivoPonto = curl_init($_SERVER['SERVER_NAME']."/uploads/pontos/".$pontoObj['arquivo']);
		curl_setopt($arquivoPonto, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($arquivoPonto, CURLINFO_HEADER_OUT, true);
		$resultado = curl_exec($arquivoPonto);
		curl_close($arquivoPonto);

		$linha = array();
		$linha = explode("\n", $resultado);

		$dadosPonto = $this->find(array('id_rh_registro_ponto = ?' => $idPonto),'nsr DESC')->current();
		$dadosPontoOk = $dadosPonto['nsr'] ? $dadosPonto['nsr'] : '0';
		$newLinha = array_slice($linha, $dadosPontoOk);
		$date = new Zend_Date();
		$i = '0';

		foreach (array_slice($linha, $dadosPontoOk) as $line){

			$line = trim($line);
			$nre = ltrim(substr($line, 0, 9), "0");
			$data = substr($line, 14, 4).'-'.substr($line, 12, 2).'-'.substr($line, 10, 2);
			$hora = substr($line, 18, 4)."00";
			$pis = ltrim(substr($line, 22, 12), "0");

			$verificaPis = $documentosBo->find(array('pis = ?' => $pis))->current();
			$verificarRegistro = $this->find(array('nsr = ?' => $nre, 'data = ?' => $data, 'hora = ?' => $hora, 'pis = ?' => $pis, 'id_rh_registro_ponto = ?' => $idPonto))->count();

			if (strlen($line) == 34 && substr($line, 9, 1) == 3 && $verificarRegistro == 0) {

				$i++;

				$newPonto = $this->_dao->createRow();
				$newPonto->nsr = $nre;
				$newPonto->data = $data;
				$newPonto->hora = $hora;
				$newPonto->pis = $pis;
				$newPonto->id_rh_registro_ponto = $idPonto;
				$newPonto->id_criacao_usuario = Zend_Auth::getInstance()->getIdentity()->usu_id;
				$newPonto->dt_criacao = $date->toString('yyyy-MM-dd');

				if ($verificaPis != null) {
					$newPonto->id_rh_funcionario = $verificaPis['id_rh_funcionario'];
				}

				$newPonto->save();

			}else{
			}
		}

		return $resposta = array('success' => true, 'total' => $i);

	}


	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
	    $object->data = $this->dateYmd($object->data);
	}
	
	public function ordenarByRequest($request)
	{
		foreach ($request['posicao'] as $posicao=>$id){
			$dadosPonto = $this->get($id);
			if(!empty($dadosPonto->id_rh_dados_ponto)){
				$dadosPonto->posicao = $posicao;
				$dadosPonto->save();
			}
		}
	}
}
