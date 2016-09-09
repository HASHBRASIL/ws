<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  02/05/2014
 */
class Processo_RelatorioController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Relatorio
     */
    protected $_bo;

    public function init()
    {
    	$this->_bo = new Processo_Model_Bo_Relatorio();
        parent::init();
        $this->_helper->layout->setLayout('novo_hash');
    }

    public function indexAction()
    {
        $workspaceSession = new Zend_Session_Namespace('workspace');
        $statusBo = new Processo_Model_Bo_Status();
//        $workspaceBo = new Auth_Model_Bo_Workspace();



//        if( $workspaceSession->free_access){
//            $this->view->workspacePairs = $workspaceBo->getPairs();
//        }
//
//        $this->view->id_workspace = $workspaceSession->id_workspace;
//        $this->view->statusPairs  = $statusBo->getPairs();
    }

    public function gridAction()
    {
        $request = $this->getAllParams();
        $this->view->processoList = $this->_bo->getRelatorioProcesso($request);
    }

    public function pdfAction(){
        if($this->getRequest()->isPost()){
            $pdf = new App_Util_Pdf(null, null, null, null, null, $orientacao = "L");
            $request = $this->getAllParams();
            $this->view->processoList = $this->_bo->getRelatorioProcesso($request);
            $this->view->request = $request;
            $html = $this->view->render('relatorio/pdf.phtml');

            $pdf->modificarFonte('helvetica', 10);
            $pdf->adicionarHtml($html);
            $pdf->abrirArquivo();exit();

        }
    }

    public function pdfAnaliticoAction(){
    	if($this->getRequest()->isPost()){
    		$date = new Zend_Date();
    		$pdf = new App_Util_Pdf(null, null, null, null, null, "L","mm", "A4", "UTF-8", " \nRelatório financeiro por cliente - emitido em: ".$date->toString('dd/MM/yyyy'));

    		$request = $this->getAllParams();
    		$this->view->processoList = $this->_bo->getRelatorioAnalitico($request);
    		$this->view->request = $request;
    		$html = $this->view->render('relatorio/pdf-analitico.phtml');
    		$pdf->modificarFonte('helvetica', 10);
    		$pdf->adicionarHtml($html);
    		$pdf->abrirArquivo();exit();

    	}
    }

}