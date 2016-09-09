<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  09/05/2013
 */
class Material_RelatorioController extends App_Controller_Action_AbstractCrud
{


    public function notaFiscalAction()
    {
        $empresaGrupo = new Empresa_Model_Bo_EmpresaGrupo();
        $pessoaBo     = new Auth_Model_Bo_Pessoal();

        $this->view->empresaGrupoCompo = $empresaGrupo->getPairs(false);
        $this->view->pessoaCombo       = $pessoaBo->getPairs(false);
    }

    public function gerarAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $html = $this->view->render('relatorio/nota.phtml');

        $pdf = new App_Util_Pdf();
        $pdf->adicionarHtml($html);
        $pdf->abrirArquivo();
    }
}