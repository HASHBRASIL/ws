<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  17/12/2013
 */
class Processo_ExtratoController extends App_Controller_Action_AbstractCrud
{


    public function init()
    {
        parent::init();
        $this->_helper->layout->setLayout('novo_hash');
        $this->_aclActionAnonymous = array("quick-search-ajax", 'get', 'autocomplete');
    }

    public function searchAction()
    {
        $statusProcessoBo       = new Processo_Model_Bo_Status();
        $this->view->comboStatus        = $statusProcessoBo->getPairs(false);
    }

    public function pdfAction()
    {
        $processoBo = new Processo_Model_Bo_Processo();
        $pdf        = new App_Util_Pdf(null, null, null, "logo4-01.jpg", "logo4-01.jpg", "P");
        $allParams  = $this->getAllParams();
        $this->view->listProcesso  = $processoBo->searchProcessoWithFinancial($allParams);
        $this->view->dt_inicio     = $this->getParam('dt_inicio');
        $this->view->dt_fim        = $this->getParam('dt_fim');

        $html = $this->view->render('extrato/pdf.phtml');

        $pdf->modificarFonte('helvetica', 10);
        $pdf->adicionarHtml($html);

        $pdf->abrirArquivo('extrato_processo.pdf');exit();

    }

}