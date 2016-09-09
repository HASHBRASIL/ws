<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  06/05/2013
 */
class Material_EstoqueController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_Estoque
     */
    protected $_bo;

    public function init()
    {
        $this->_helper->layout()->setLayout('metronic');
    	$this->_aclActionAnonymous = array('extrato',"sum-estoque","get-lote", 'autocomplete-lote');
        $this->_bo = new Material_Model_Bo_Estoque();
        parent::init();
    }

    public function _initForm()
    {
        $this->_id = $this->getParam('id_estoque');
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $item = $this->_bo->get($id);
        if(empty($item->id_estoque)){
            $this->_helper->json(array('success' => false));
        }
        $itemJson = array();

        foreach ($item as $key => $value){
            $itemJson[$key] = $value;
        }
         $itemJson['nome_item'] = $item->getItem()?$item->getItem()->nome:null;
         $itemJson['success']   = true;

         $this->_helper->json($itemJson);
    }

    public function sumEstoqueAction()
    {
        $idItem         = $this->getParam('idItem');
        $idOpcao        = $this->getParam('id_opcao');
        $idWorkspace    = $this->getParam('id_workspace');
        $qtdEstoque = $this->_bo->sumItemEstoque($idItem, $idOpcao, $idWorkspace);
        $this->_helper->json(array('qtd_estoque' => $qtdEstoque));
    }

    public function getLoteAction()
    {
        $qtdSolicitada = $this->getParam('qtd_solicitada');
        $idItem        = $this->getParam('id_item');
        $idOpcao       = $this->getParam('id_opcao');
        $idWorkspace   = $this->getParam('id_workspace');
        $loteList      = $this->_bo->getLote($qtdSolicitada, $idItem, $idOpcao,$idWorkspace);
        $this->_helper->json($loteList);
    }

    public function autocompleteLoteAction()
    {
        $term             = $this->getRequest()->getParam('term');
        $id_workspace     = $this->getRequest()->getParam('id_workspace');
        $id_item          = $this->getRequest()->getParam('id_item');
        $opcaoArray       = $this->getParam('id_opcao');
        $list             = $this->_bo->getAutocompleteLote($term, $id_item, $id_workspace, $opcaoArray);
        $this->_helper->json($list);
    }

    public function gridAction()
    {
        $itemBo         = new Material_Model_Bo_Item();
        $criteria       = array('ativo = ?'=> App_Model_Dao_Abstract::ATIVO);
        $itemList       = $itemBo->find($criteria);

        $this->view->itemList = $itemList;
    }

    public function detailItemAction()
    {
        $this->_helper->layout->disableLayout();
        $estMovBo     = new Material_Model_Bo_EstoqueMovimento();
        $idItem       = $this->getParam("id_item");

        $this->view->listEstoque = $estMovBo->getDetailByItem($idItem);
    }

    public function loteAction()
    {
        $idItem        = $this->getParam('idItem');
        $allParams     = $this->getAllParams();
        $workspaceList = Zend_Auth::getInstance()->getStorage()->read()->workspace;

        $this->view->paginator = $this->_bo->paginatorByItem($allParams, $idItem);
        $this->view->idItem = $idItem;
        $this->view->workspaceCombo = $workspaceList;
        $this->view->id_workspace = 14;

    }

    public function extratoAction()
    {
        $this->_helper->layout->disableLayout();
        $itemEntregaBo = new Material_Model_Bo_ItemEntrega();
        $idItem = $this->getParam('id_item');
        $this->view->totalEstoque  = $this->_bo->getTotalByItem($idItem);
        $this->view->totalSolicitado = $itemEntregaBo->sumQtdItem($idItem);
    }

    /**
     * Metodo responsável por fazer um extrato detalhado do estoque
     * por onde houve a entrada ou a saida daquele lote dentro do sistema
     * se ele foi transferido.
     */
    public function extratoLoteAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            $this->_helper->layout->disableLayout();
        }
        $id_estoque = $this->getParam('id_estoque');
        $estoqueMovimentoBo = new Material_Model_Bo_EstoqueMovimento();
        $this->view->estoqueMovimentoList = $estoqueMovimentoBo->find(array('id_estoque = ?' => $id_estoque));
    }

}