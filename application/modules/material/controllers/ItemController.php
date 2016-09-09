<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  08/04/2013
 */
class Material_ItemController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_Item
     */
    protected $_bo;
    protected $_aclActionAnonymous = array("autocomplete-estoque","autocomplete","get", 'find-produto-json');

    /**
     * irá sempre passar quanto o controller for acessado herdando do metodo da mãe.
     * Criando o objeto do BO do controller.
     * Adicionando o redirecionamento para a grid do item com o id_grupo que é obrigado.
     * @see App_Controller_Action_AbstractCrud::init()
     */
    public function init()
    {
        $this->_helper->layout()->setLayout('metronic');
        $this->_bo   = new Material_Model_Bo_Item();

        $idGrupo         = $this->getParam('id_grupo');
        $idsubgrupo      = $this->getParam('id_subgrupo');
        $idclasse        = $this->getParam('id_classe');

        $this->_redirectDelete = $this->params('material/item/grid',
                                                                array('id_grupo'     => $idGrupo,
                                                                      'id_subgrupo'  => $idsubgrupo,
                                                                      'id_classe'    => $idclasse
                                                                     )
                                                                );
        parent::init();
        $this->_redirectUnauthorizedWorkspace = "grid";
    }

    /**
     * verificar se possui id_grupo se não possuir será redirecionado
     * criar uma lista de item é manda para a view
     * @return void
     */
    public function gridAction()
    {
        $this->_verificarGrupo();
        $grupoBo         = new Material_Model_Bo_Grupo();

        $idGrupo         = $this->getParam('id_grupo');
        $idsubgrupo      = $this->getParam('id_subgrupo');
        $idclasse        = $this->getParam('id_classe');

        $this->view->comboGrupo = $grupoBo->getPairs();
        $this->view->listItem  = $this->_bo->getListItem($idGrupo, $idsubgrupo, $idclasse);
    }

    public function deleteAction()
    {
        $this->_verificarGrupo();
        parent::deleteAction();
    }

    /**
     * Mostra o formulário para o usuario
     * Se vier via post será salvo
     * @return void
     */
    public function _initForm()
    {
        $this->_verificarGrupo();
        $unidadeBo   = new Sis_Model_Bo_TipoUnidade();
        $atributoBo  = new Material_Model_Bo_Atributo();
        $workspaceBO = new Auth_Model_Bo_Workspace();
        $produto     = $this->_bo->get($this->_id);

        $this->view->comboAtributo  = array(null => '------ Selecione ------')+$atributoBo->getPairs();
        $this->view->tipoUnidade    = array('' =>'------ Selecione ------') + $unidadeBo->getPairs(false);

    }
    public function formAction()
    {
        $id_duplicate   = $this->getParam('id_duplicate');
        parent::formAction();
        $object = $this->view->vo;
        if(!empty($id_duplicate) && empty($object->id_item)){
            $object = $this->_bo->duplicate($object, $id_duplicate);
            $object->setFromArray($this->getAllParams());
            $grupoBo = new Material_Model_Bo_Grupo();

            $this->view->disableGroup = true;
            $this->view->grupoCombo = array('----Selecione ----')+$grupoBo->getPairs();
        }
        if(empty($object->id_item)){
            $object->materia_prima = 1;
        }
        $this->view->vo = $object;
        $id = $this->getParam('id');
        if(empty($id) && !empty($object->id_item)){
            $url = $this->params('material/item/form/id/'.$object->id_item,
                    array('id_grupo'     => $this->getParam('id_grupo'),
                            'id_subgrupo'  => $this->getParam('id_subgrupo'),
                            'id_classe'    => $this->getParam('id_classe')
                    )
            );
            $this->_redirect($url);
        }
    }

    private function params($baseUrl, $params)
    {
        $param = "";
        if(!empty($params)){
            foreach ($params as $name => $value){
                if(!empty($name) && !empty($value)){
                    $param .= "/".$name."/".$value;
                }
            }
        }
        return $this->view->baseUrl($baseUrl.$param);
    }

    private function _verificarGrupo()
    {
        $grupoBo     = new Material_Model_Bo_Grupo();
        $subgrupoBo  = new Material_Model_Bo_Subgrupo();
        $classeBo    = new Material_Model_Bo_Classe();

        $idGrupo         = $this->getParam('id_grupo');
        $idsubgrupo      = $this->getParam('id_subgrupo');
        $idclasse        = $this->getParam('id_classe');

        if(empty($idGrupo) && empty($idsubgrupo) && empty($idclasse)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um grupo, subgrupo ou classe.");
            $this->redirect('material/grupo/index');
        }

        $this->view->grupo         = $grupoBo->get($idGrupo);
        $this->view->subgrupo      = $subgrupoBo->get($idsubgrupo);
        $this->view->classe        = $classeBo->get($idclasse);
    }

    public function treeAction()
    {
        $this->_helper->layout->disableLayout();
        $itemBo                           = new Material_Model_Bo_Item();
        $grupoBo                          = new Material_Model_Bo_Grupo();

        $criteria                         = array('ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        $this->view->listGrupo            = $grupoBo->find($criteria);
        $this->view->idGrupoExist         = $itemBo->getIdGrupo();
        $this->view->idSubgrupoExist      = $itemBo->getIdSubgrupo();
    }

    public function getAction()
    {
        $id = $this->getParam('id_item');
        $item = $this->_bo->get($id);
        if(empty($item->id_item)){
            $this->_helper->json(array('success' => false));
            App_Validate_MessageBroker::addErrorMessage('Não possui produto');
        }
        $itemJson = array();

        foreach ($item as $key => $value){
            $itemJson[$key] = $value;
        }
        $itemJson['nome_unidade_compra']     = $item->getUnidadeCompra()->nome;
        $itemJson['nome_unidade_consumo']    = $item->getUnidadeConsumo()->nome;
        $itemJson['success']   = true;

        $this->_helper->json($itemJson);
    }

    public function autocompleteEstoqueAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocompleteToSumEstoque($term);
        $this->_helper->json($list);
    }

    public function gridOpcaoAction()
    {
        $this->_helper->layout()->disableLayout();
        $idItem = $this->getParam('id_item');
        $itemOpcaoBo = new Material_Model_Bo_ItemOpcao();
        $this->view->itemOpcaoList = $itemOpcaoBo->findOpcaoByItem($idItem);
    }

    public function findProdutoJsonAction()
    {
        $request = $this->getRequest()->getParams();
        $listProduto = $this->_bo->findProdutoByRequest($request);
        $this->_helper->json(array('count' => count($listProduto), 'list' => $listProduto));
    }

    public function mudarGrupoAction()
    {

        $request = $this->getAllParams();
        if($this->getRequest()->isPost()){
            try {
                $this->_bo->mudarGrupo($request);
                $response = array( 'success' => true);
                App_Validate_MessageBroker::addSuccessMessage("Produto migrado com sucesso");
                $this->_helper->json($response);
            }
            catch (Exception $e){
                //verifica se e pelo ajax
                $response = array('success' => false, 'message'=>'Ocorreu um erro inesperado. Entre em contato com o administrador.', "message-exception" => $e->getMessage() );
                $this->_helper->json($response);
            }
        }
    }
}