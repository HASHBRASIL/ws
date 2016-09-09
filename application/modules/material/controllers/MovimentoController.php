<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  27/05/2013
 */
class Material_MovimentoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_Movimento
     */
    protected $_bo;

    public function init()
    {
        $this->_bo   = new Material_Model_Bo_Movimento();
        parent::init();
    }

    public function gridLoteAction()
    {
        //desabilitando o limite de memória de processamento do servidor
        ini_set( "memory_limit", -1 );
        //desabilitando o limite do tempo de execução
        ini_set( "max_execution_time", 0 );

        $this->_helper->layout->disableLayout();
        $id_movimento         = $this->getParam('id_movimento');
        $movimentoList        = $this->_bo->get($id_movimento);
        $this->view->movimentoList = $movimentoList;
    }

    public function movimentoByProcessoAction()
    {
        if($this->getRequest()->isXmlHttpRequest()){
            $this->_helper->layout()->disableLayout();
        }

        $pro_id = $this->getParam('pro_id');
        $this->view->itemList = $this->_bo->getSaidaByProcesso($pro_id);
    }

    public function gridLoteProcessoAction()
    {
        //desabilitando o limite de memória de processamento do servidor
        ini_set( "memory_limit", -1 );
        //desabilitando o limite do tempo de execução
        ini_set( "max_execution_time", 0 );

        $this->_helper->layout->disableLayout();
        $idMaterialProcesso     = $this->getParam('id_material_processo');
        $filtro     			= array('id_material_processo'=> $idMaterialProcesso);
        $movimentoList        	= $this->_bo->getAllByAny($filtro);
        $this->view->movimentoList = $movimentoList;
    }

    public function transferAction()
    {
        $id_estoque = $this->getParam('id_estoque');
        $id_workspace = $this->getParam('id_workspace');

        $estoqueBo = new Material_Model_Bo_Estoque();
        $estoque = $estoqueBo->get();
        try {
            $estoqueBo->transferWorkspace($estoque, $id_estoque, $id_workspace);
            $this->_bo->saveTransferEstoque($estoque, $id_estoque);
            App_Validate_MessageBroker::addSuccessMessage("Lote transferido com sucesso");
            $this->_helper->json(array('success' => true));
        } catch (Exception $e) {
            exit('erro - '.$e->getMessage());
        }


    }


}