<?php

/**
 * @author Vinicius S P Leônidas
 * @since 01/11/13
 */
class Compra_RelatorioCompraController extends App_Controller_Action_AbstractCrud {

    /**
     * @var Compra_Model_Bo_Compra
     */
	protected $_bo;

	public function init()
	{
		$this->_bo = new Compra_Model_Bo_Compra();
		$this->_helper->layout()->setLayout('metronic');
		parent::init();
	}

	public function gridAction(){
		
	}
	
	public function gridCompraAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->view->compraList = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "finalizado = ?" => App_Model_Dao_Abstract::ATIVO));
	}
	
	public function viewCompraAction()
	{
		$compraBo = new Compra_Model_Bo_Compra();
		$compraItem = new Compra_Model_Bo_CompraItem();
		$itemOpcaoBo = new Compra_Model_Bo_CompraItemOpcao();
		$campanhaBo = new Compra_Model_Bo_Campanha();
		
		$id_compra = $this->getParam('id_compra');
		$id_campanha = $this->getParam('id_campanha');
		
		$this->view->compra = $compraBo->find(array("id_compra = ?" => $id_compra, "ativo = ?" => App_Model_Dao_Abstract::ATIVO))->current();
		$this->view->listCampanha = $campanhaBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_campanha = ?' => $id_campanha))->current();
		$this->view->compraList = $compraItem->find(array("id_compra = ?" => $id_compra, "ativo = ?" => App_Model_Dao_Abstract::ATIVO));
		
		$arrayOpcao = array();
		
		foreach ($this->view->compraList as $Itens){
			$resposta = $itemOpcaoBo->getOpcao($Itens['id_compra_item']);
			if(count($resposta) > 1)
				$arrayOpcao[$Itens['id_compra_item']] = $itemOpcaoBo->getOpcao($Itens['id_compra_item']);
				
		}
		
		$this->view->opcao = $arrayOpcao;
	}
	public function gridCampanhaAction()
	{
		$this->_helper->layout()->disableLayout();
		$campanhaBo = new Compra_Model_Bo_Campanha();
		$this->view->listCampanha = $campanhaBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO));
	}
	public function itensCompradoAction()
	{
		$compraItemBo = new Compra_Model_Bo_CompraItem();
		$compraItemOpcao = new Compra_Model_Bo_CompraItemOpcao();
		
		$id_campanha = $this->getParam('id_campanha');
		$listProdutoComprado = $compraItemBo->findProdutoComprado($id_campanha);

		$campanhaBo = new Compra_Model_Bo_Campanha();
		$this->view->listCampanha = $campanhaBo->find(array('ativo = ?' => App_Model_Dao_Abstract::ATIVO, 'id_campanha = ?' => $id_campanha))->current();
		
		$listItem = array();
		foreach($listProdutoComprado as $compraItem){
			//metodo que compara listItem possui este id_item e se são as msm opção
			//@return existe as tres comparaçoes vai me retorna a chave que esta $listItem se não false
			$keyCompare = $this->_bo->comparaItens($compraItem, $listItem);
			if($keyCompare !== false){
				$listItem[$keyCompare]['quantidade'] += $compraItem['quantidade'];
			}else{
				$listItem[] = $compraItem;
			}
		}
		$this->view->listaItens = $listItem;

	}

}