<?php
class Financial_CreditoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Financial_Model_Bo_Credito
     */
    protected $_bo;
    protected $_redirectDelete = "/financial/grid";

    public function init()
    {
    	$this->_bo = new Financial_Model_Bo_Credito();
        parent::init();
    }
	
	public function _initForm(){
		
		$this->_id = $this->getParam("id_credito");
		
		$empresaBo			 				= new Empresa_Model_Bo_Empresa();
		
		$this->view->comboFuncionarios		= $empresaBo->getFuncionarioPairs();
		
	}
	
	public function gridAction(){
	
		$this->view->creditoList = $this->_bo->find(array("ativo = ?"=> App_Model_Dao_Abstract::ATIVO));
		
	}
	
	public function quickSearchAjaxAction(){
	
		$id = $this->_bo->get($this->getParam('id'));
		if ($this->_id != ""){
			$this->_helper->json(array("success" => "true"));
		}else{
			$this->_helper->json(array("success" => "false"));
		}
	}

	public function limiteByEmpresaAjaxAction(){
		
		$this->_helper->layout->disableLayout();
		
		$id = $this->getParam('id');
		
		$financialObj 					= new Financial_Model_Bo_Financial();
		$financialList = $financialObj->findFinancialForGetLimiteAjax($id);
		$valorTotal = "0";
		foreach ($financialList as $key => $financial){
			$valorTotal = $valorTotal + $financial->fin_valor;
		}
		$this->view->creditoList = $this->_bo->find(array("empresas_id = ?"=>$id), "id_credito DESC", 5);
		
		$this->view->financialList = $financialList;
		$this->view->valorTotal = $valorTotal;
	}

}