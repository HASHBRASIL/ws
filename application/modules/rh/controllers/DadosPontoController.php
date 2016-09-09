<?php
/**
 * @author Vinicius Leônidas
 * @since 17/06/2014
 */
class Rh_DadosPontoController extends App_Controller_Action_AbstractCrud{

    /**
     * @var Rh_Model_Bo_DadosPonto
     */
	protected $_bo;

	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_DadosPonto();
		parent::init();
		$this->_id = $this->_getParam('id_rh_dados_ponto');
		$this->_redirectDelete = str_replace("'", "", $this->getParam('url', "/rh/dados-ponto/grid"));
	}

	public function gridAction()
    {
        $allParams = $this->getRequest()->getParams();
        if(isset($allParams['searchString'])){
            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
        }

        $paginator = $this->_bo->paginator($allParams);

        $this->view->paginator = $paginator;
        $this->view->workspaceSession     = new Zend_Session_Namespace('workspace');
    }
    
    public function ordenarAction()
    {
    	try {
    		$this->_bo->ordenarByRequest($this->getAllParams());
    		$this->_helper->json(array('success' =>true, 'message'=>'Dados reordenados com sucesso.'));
    	} catch (Exception $e) {
    		$this->_helper->json(array('success' => false, 'message'=>'Não foi possivel ordenar.'));
    	}
    }

}