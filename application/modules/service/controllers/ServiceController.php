<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  08/04/2013
 */
class Service_ServiceController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Service_Model_Bo_Servico
     */
    protected $_bo;

    /**
     * @var string
     */
    protected $_redirectDelete = "service/service/grid";

    public function init()
    {
    		$this->_helper->layout()->setLayout('metronic');
        $this->_bo   = new Service_Model_Bo_Servico();

        $idGrupo         = $this->getParam('id_grupo');
        $idsubgrupo      = $this->getParam('id_subgrupo');
        $idclasse        = $this->getParam('id_classe');

        $this->_redirectDelete = $this->params('service/service/grid',
                                                                array('id_grupo'     => $idGrupo,
                                                                      'id_subgrupo'  => $idsubgrupo,
                                                                      'id_classe'    => $idclasse
                                                                     )
                                                                );
        $this->_aclActionAnonymous = array('autocomplete');
        parent::init();
    }

    public function indexAction()
    {
    }

    public function gridAction()
    {
        $this->_verificarGrupo();
        $idGrupo         = $this->getParam('id_grupo');
        $idsubgrupo      = $this->getParam('id_subgrupo');
        $idclasse        = $this->getParam('id_classe');

        $this->view->serviceList   = $this->_bo->getAll($idGrupo, $idsubgrupo, $idclasse);
    }

    public function formAction()
    {
        $this->_verificarGrupo();
        $tipoUnidadeBo     = new Service_Model_Bo_TipoUnidade();

        $id_servico   = $this->getParam('id_servico');

        $objectServico   = $this->_bo->get($id_servico);
        // verificar se vem via post
        if($this->getRequest()->isPost()){
            $request         = $this->getAllParams();

            try {
                $serviço = $this->_bo->saveFromRequest($request, $objectServico);
                if(empty($id_servico)){
                    App_Validate_MessageBroker::addSuccessMessage("Serviço cadastrado com sucesso.");
                }else {
                    App_Validate_MessageBroker::addSuccessMessage("Serviço atualizado com sucesso.");
                }
            }
            catch (App_Validate_Exception $e){
            }
            catch (Exception $e){
                App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro inesperado.");
            }
        }
        $idFornecedor  = $this->getParam('id_empresa_fornecedor');
        $idCentroCusto = $this->getParam('id_centro_custo');

        //verifica se vem fornecedor no request
        $fornecedorBo                 = new Empresa_Model_Bo_Fornecedor();
        $this->view->fornecedorList   = $fornecedorBo->getFornecedorByService($idFornecedor, $id_servico);

        //verifica se vem centro de custo no request
        $centroCustoBo                 = new Service_Model_Bo_CentroCusto();
        $this->view->centroCustoList   = $centroCustoBo->getCentroByService($idCentroCusto, $id_servico);

        $this->view->servico           = $objectServico;
        $this->view->tipoUnidade       = array('' =>'------ Selecione -----')+$tipoUnidadeBo->getPairs();
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
        $grupoBo     = new Service_Model_Bo_Grupo();
        $subgrupoBo  = new Service_Model_Bo_SubGrupo();
        $classeBo    = new Service_Model_Bo_Classe();

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

}