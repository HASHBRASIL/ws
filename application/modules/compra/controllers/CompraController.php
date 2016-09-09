<?php
/**
 * @author Vinícius S P Leônidas
 * @since  21/10/2013
 */

class Compra_CompraController extends App_Controller_Action_AbstractCrud
{
	/**
	 * @var Compra_Model_Bo_Compra
	 */
	protected $_bo;
	protected $_redirectDelete = '/compra/compra/grid';
	
	public function init()
	{
		$this->_bo = new Compra_Model_Bo_Compra();
		$this->_aclActionAnonymous = array('autocomplete', 'autocomplete-nome', 'form');
		$this->_helper->layout()->setLayout('metronic');
		parent::init();
	}
	public function gridAction(){
		
		$this->view->compraList = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO, "id_consultor = ?" => Zend_Auth::getInstance()->getIdentity()->id));
		
	}
	public function autocompleteAction()
	{
		$id = $this->getParam('id');
		$term = $this->getRequest()->getParam('term');
		$campanhaItem = new Compra_Model_Bo_CampanhaItem();
		$list = $campanhaItem->getItensReferencia($id, $term);
		$this->_helper->json($list);
	}

	public function autocompleteNomeAction()
	{
		$id = $this->getParam('id');
		$term = $this->getRequest()->getParam('term');
		$campanhaItem = new Compra_Model_Bo_CampanhaItem();
		$list = $campanhaItem->getItensNome($id, $term);
		$this->_helper->json($list);
	}
	
	public function compraAction()
	{

		$this->view->idCompra = $this->_getParam('id');
		$this->view->idCampanha = $this->_getParam('id_campanha');
		
		$campanhaCorporativo = new Compra_Model_Bo_CampanhaCorporativo();
		$this->view->campanhas = $campanhaCorporativo->getCampanhasCorporativa();

	}
	public function pesquisaCampanhaAction(){

		$this->_helper->layout()->disableLayout();
		
		$campanhaCorporativo = new Compra_Model_Bo_CampanhaCorporativo();
		$campanhaItem = new Compra_Model_Bo_CampanhaItem();
		$id = $this->getParam('id');

		$this->view->campanha = $campanhaCorporativo->find(array("id_campanha = ?" => $id , "id_corporativa = ?" => Zend_Auth::getInstance()->getIdentity()->id));

		$this->view->id = $id;
		$this->view->ok = $this->_bo->contarComprasRelaizadas($id);
	}
	public function pesquisaProdutoAction(){

		$this->_helper->layout()->disableLayout();
		
		$campanhaItem = new Compra_Model_Bo_CampanhaItem();
		$arquivoItem = new Material_Model_Bo_Arquivo();
		$itemOpcaoBo = new Material_Model_Bo_ItemOpcao();
		$compraItemBo = new Compra_Model_Bo_CompraItem();
		
		$id_campanha = $this->_getParam('id');
		$term = $this->_getParam('tipo');
		$valor = $this->_getParam('nome');
		$idCompraItem = $this->getParam('id_compra_item');
		$idItem = $this->getParam('id_item');
		
		if (!empty($idItem)) {
			$this->view->compra = $compraItemBo->find(array('id_compra_item = ?' => $idCompraItem))->current();
		}
		
		$pesquisaProduto = $campanhaItem->getItens($id_campanha, $idItem, $term, $valor);
		
		
		if ($pesquisaProduto == false) {
			$this->view->produto = false;
		} else {
			$this->view->produto = $pesquisaProduto;
			if (!empty($idItem)) {
				$this->view->itemOpcaoList = $itemOpcaoBo->findOpcaoByItem($idItem);
				$criteria   = array(
						'id_item = ?' => $idItem,
						'ativo = ?'   => App_Model_Dao_Abstract::ATIVO
				);			
				
				$opcaoBo = new Compra_Model_Bo_CompraItemOpcao();
				$resposta = $opcaoBo->getIdOpcao($idCompraItem);
				$arrayOpcao = array();
				if(count($resposta) > 1)
					$arrayOpcao = $resposta;
				$this->view->opcao = $arrayOpcao;
				
				
			} else {			
				$this->view->itemOpcaoList = $itemOpcaoBo->findOpcaoByItem($this->view->produto['id_item']);				
				$criteria   = array(
						'id_item = ?' => $this->view->produto['id_item'],
						'ativo = ?'   => App_Model_Dao_Abstract::ATIVO
				);				
			}
			
			$this->view->arquivoList = $arquivoItem->find($criteria);
		}
		
	}
	public function gridComprandoAction(){
		$this->_helper->layout()->disableLayout();

		$compraItem = new Compra_Model_Bo_CompraItem();
		$itemOpcaoBo = new Compra_Model_Bo_CompraItemOpcao();
		
		$id = $this->_getParam('id_compra');
		
		$this->view->compraList = $compraItem->find(array("id_compra = ?" => $id, "ativo = ?" => App_Model_Dao_Abstract::ATIVO));
		$teste = $this->_bo->find(array('id_compra = ?' => $id))->current();
		$this->view->idCompra = $id;
		
		$arrayOpcao = array();

		foreach ($this->view->compraList as $Itens){	
			$resposta = $itemOpcaoBo->getOpcao($Itens['id_compra_item']);
			if(count($resposta) > 1)
				$arrayOpcao[$Itens['id_compra_item']] = $itemOpcaoBo->getOpcao($Itens['id_compra_item']);
			
		}
		
		$this->view->opcao = $arrayOpcao;
	}
	public function finalizarCompraAction(){
		
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout()->disableLayout();
		
		$request =  $this->getAllParams();
		
		
		$compraObj = $this->_bo->get($request['id_compra']);
		
		$request['finalizado'] = App_Model_Dao_Abstract::ATIVO;
		try {
			$this->_bo->saveFromRequest($request, $compraObj);
			if($this->getRequest()->isXmlHttpRequest()){
	      $response = array('success' => true, 'message');
				$this->_helper->json($response);
			}
		} catch (App_Validate_Exception $e) {
			if($this->getRequest()->isXmlHttpRequest()){
	      $response = array('success' => false, 'message'=> $this->_mensagemJson());
	      $this->_helper->json($response);
      }
		} catch (Exception $e){
			if($this->getRequest()->isXmlHttpRequest()){
				$response = array('success' => false, 'message'=> $e->getMessage());
				$this->_helper->json($response);
			}
		}
		
	}
}