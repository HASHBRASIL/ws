<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/12/2013
 */
class Processo_PlanejamentoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_Planejamento
     */
    protected $_bo;

    public function init()
    {
    	$this->_bo = new Processo_Model_Bo_Planejamento();
        parent::init();
        $this->_helper->layout->setLayout('novo_hash');
        $this->_id = $this->getParam('id_planejamento');
        $this->_aclActionAnonymous = array('get');
    }

    public function _initForm()
    {
    }

    public function gridByDateAction()
    {
        $this->_helper->layout()->disableLayout();
        $date = $this->getParam('data');
        $date = new Zend_Date($date);

        $workspaceSession     = new Zend_Session_Namespace('workspace');
        $criteria             = array(
                                    'data = ?' => $date->toString('yyyy-MM-dd'),
                                    'ativo = ?' => App_Model_Dao_Abstract::ATIVO
                                    );
        if(!$workspaceSession->free_access){
            $criteria['id_workspace = ?'] = $workspaceSession->id_workspace;
        }

        $this->view->listPlanejamento = $this->_bo->find($criteria, 'ordem asc');
        $this->view->date = $date->toString('yyyy-MM-dd');
    }

    public function getAction()
    {
        $idPlanejamento = $this->getParam('id_planejamento');
        $planejamento = $this->_bo->get($idPlanejamento);
        $planejamentoArray = $planejamento->toArray();
        $planejamentoArray['cod_processo'] = $planejamento->getProcesso()->pro_codigo;
        $this->_helper->json($planejamentoArray);
    }

    public function pdfByDateAction()
    {
        $this->_helper->layout()->disableLayout();

        $pdf = new App_Util_Pdf();
        $date = $this->getParam('data');
        $date = new Zend_Date($date);



        $workspaceSession     = new Zend_Session_Namespace('workspace');
        $criteria             = array(
                'data = ?' => $date->toString('yyyy-MM-dd'),
                'ativo = ?' => App_Model_Dao_Abstract::ATIVO
        );
        if(!$workspaceSession->free_access){
            $criteria['id_workspace = ?'] = $workspaceSession->id_workspace;
        }

        $this->view->listPlanejamento = $this->_bo->find($criteria, 'ordem asc');
        $this->view->date = $date->toString('yyyy-MM-dd');

        $html = $this->view->render('planejamento/pdf-by-date.phtml');

        $pdf->modificarFonte('helvetica', 10);
        $pdf->adicionarHtml($html);
        $pdf->abrirArquivo();exit();
    }

}