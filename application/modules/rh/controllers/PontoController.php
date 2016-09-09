<?php
/**
 * @author Vinicius Leônidas
 * @since 29/01/2014
 */
class Rh_PontoController extends App_Controller_Action_AbstractCrud{

	/**
	 * @var Rh_Model_Bo_Ponto
	 */
	protected $_bo;

	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_Ponto();
		parent::init();
		$this->_id = $this->_getParam('id_ponto');
		$this->_redirectDelete = "/rh/ponto/grid";
 		$this->_aclActionAnonymous = array('relatorio', 'count-duplicado');
	}

	public function importaPontosAction(){

		$dadosDoPontoBo = new Rh_Model_Bo_DadosPonto();
 		$this->noRenderAndNoLayout();
		$this->_helper->json($dadosDoPontoBo->migrarTxt($this->_id));

	}

	public function gridAction(){

		$this->view->iten = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO));

	}

	public function folhaDePontoAction(){

		$funcionarioBo = new Rh_Model_Bo_Funcionario();
		$justificativaBo = new Rh_Model_Bo_JustificativaPonto();
		$workspaceSession = new Zend_Session_Namespace('workspace');

		$this->view->ponto = array(null => 'Todos')+$this->_bo->getPairs();
		$this->view->comboPessoa       = array(null => '---- Selecione ----')+$funcionarioBo->getIdFuncionario(array('trf.ativo = ?' => App_Model_Dao_Abstract::ATIVO, "id_workspace = ?" => $workspaceSession->id_workspace));
		$this->view->comboJustivicativa = array(null => '---- Selecione ----')+$justificativaBo->getJustificativa(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));

	}

	public function salvaPontoManualAjaxAction(){

		$this->noRenderAndNoLayout();
		$dadosDoPontoBo = new Rh_Model_Bo_DadosPonto();
		$this->_helper->json($dadosDoPontoBo->savePontoManual($this->_getAllParams()));

	}

	public function salvaMotivoPontoManualAjaxAction(){

		$this->noRenderAndNoLayout();
 		$dadosDoPontoBo = new Rh_Model_Bo_DadosPonto();
 		$this->_helper->json($dadosDoPontoBo->saveMotivoPontoManual($this->_getAllParams()));

	}

	public function gridFolhaDePontoAjaxAction(){

		$this->_helper->layout()->disableLayout();

		$ponto = $this->_getParam('ponto');
		$func = $this->_getParam('funcionario');

		$configuracaoBo = new Rh_Model_Bo_Configuracao();
		$datePeriodo = $configuracaoBo->getFechamentoFolha($this->_getParam('dataInicial'));

		if(count($datePeriodo) == 0){
			$this->_helper->json(array('success' => false, 'message' => 'Não possui configuração de folha de ponto para este workspace'));
		}

		$dadosDoPontoBo = new Rh_Model_Bo_DadosPonto();
		$this->view->dados = $dadosDoPontoBo->folhaPonto($datePeriodo['data_inicial'], $datePeriodo['data_fim'], $func);
	}

	public function relatorioAction()
	{
		$ponto 		= $this->_getParam('ponto');
		$inicio 	= $this->_getParam('dataInicial');
		$func 		= $this->_getParam('funcionario');

		$configuracaoBo 	= new Rh_Model_Bo_Configuracao();
		$dadosPontoBo	 	= new Rh_Model_Bo_DadosPonto();
		$funcionarioBo 		= new Rh_Model_Bo_Funcionario();
		$horarioFuncionarioBo 	= new Rh_Model_Bo_HorarioFuncionario();
		$configHorarioBo		= new Rh_Model_Bo_ConfigHorario();

		$datePeriodo 		= $configuracaoBo->getFechamentoFolha($inicio, 'yyyy-MM-dd');

		if(count($datePeriodo) == 0){
			exit('Error: Não possui configuração de folha de ponto para este workspace');
		}
		$horarioPadrao = $horarioFuncionarioBo->getHorarioPadrao( $datePeriodo['data_inicial'], $datePeriodo['data_fim'], $func);
		$this->_helper->layout->disableLayout();

		$pdf = new App_Util_Pdf(null, null, false);

		$this->view->funcionario = $funcionarioBo->get($func);
		$this->view->dados = $dadosPontoBo->folhaPonto($datePeriodo['data_inicial'], $datePeriodo['data_fim'], $func);
		$this->view->data = new Zend_Date($inicio);

		$configHorarioPadrao = array();
		if(is_array($horarioPadrao)){
			foreach ($horarioPadrao as $horario){
				$configHorarioPadrao[$horario->data] = $configHorarioBo->getListHorario($horario->id_horario);
			}
		}elseif (is_object($horarioPadrao)){
			$configHorarioPadrao[] = $configHorarioBo->getListHorario($horarioPadrao->id_horario);
		}
		$this->view->configHorarioPadrao = $configHorarioPadrao;
		$html = $this->view->render('ponto/relatorio.phtml');
		$pdf->modificarFonte('helvetica', 10);
		$pdf->adicionarHtml($html);
		$pdf->abrirArquivo();exit();

	}

	public function gridDuplicadoAction()
	{
	    if(empty($this->_getParam('data_inicial'))){
	    	$this->_helper->json(array('count' => 0));
	    }
	    $configuracaoBo = new Rh_Model_Bo_Configuracao();
	    $datePeriodo = $configuracaoBo->getFechamentoFolha($this->_getParam('data_inicial'), 'yyyy-MM-dd');

	    if(count($datePeriodo) == 0){
	    	$this->_helper->json(array('count' => 0));
	    }

	    $dtInicio 		= $datePeriodo['data_inicial'];
	    $dtFim  		= $datePeriodo['data_fim'];
	    $idFuncionario 	= $this->_getParam('id_funcionario');
	    $dadosPontoBo 	= new Rh_Model_Bo_DadosPonto();

	    $this->_helper->layout->disableLayout();

	    $criteria = array(
	                    'id_rh_funcionario = ?' => $idFuncionario,
	                    'data BETWEEN "'.$dtInicio.'" and "'. $dtFim.'"',
	                    'duplicado = ?' => Rh_Model_Bo_DadosPonto::DUPLICADO
	                );
	    $pontoList = $dadosPontoBo->find($criteria);
	    if(count($pontoList) == 0)
	        $this->_helper->json(array('count' => count($pontoList)));

	    $this->view->pontoList = $pontoList;
	}

	public function countDuplicadoAction()
	{
		if(empty($this->_getParam('data_inicial'))){
			$this->_helper->json(array('count' => 0));
		}
		$configuracaoBo = new Rh_Model_Bo_Configuracao();
	    $datePeriodo = $configuracaoBo->getFechamentoFolha($this->_getParam('data_inicial'), 'yyyy-MM-dd');

	    if(count($datePeriodo) == 0){
	    	$this->_helper->json(array('count' => 0));
	    }
	    $dtInicio 		= $datePeriodo['data_inicial'];
	    $dtFim  		= $datePeriodo['data_fim'];
	    $idFuncionario 	= $this->_getParam('id_funcionario');
	    $dadosPontoBo 	= new Rh_Model_Bo_DadosPonto();

	    $criteria = array(
	                    'id_rh_funcionario = ?' => $idFuncionario,
	                    'data BETWEEN "'.$dtInicio.'" and "'. $dtFim.'"',
	                    'duplicado = ?' => Rh_Model_Bo_DadosPonto::DUPLICADO
	                );
	    $pontoList = $dadosPontoBo->find($criteria);
        $this->_helper->json(array('count' => count($pontoList)));


	}
}
