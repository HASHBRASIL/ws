<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  30/04/2013
 */
class Material_NfeController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Material_Model_Bo_Nfe
     */
    protected $_bo;

    public function init()
    {
        $this->_helper->layout->setLayout('metronic');
        $this->_bo = new Material_Model_Bo_Nfe();
        parent::init();
        $this->_redirectDelete      = "material/nfe/grid";
        $this->_redirectFormSuccess = "material/nfe/grid";

        $this->_hasWorkspace = true;
        $this->_getRegistersWithoutWorkspace = true;
    }

    public function gridAction()
    {
        $allParams = $this->getRequest()->getParams();
        if(isset($allParams['searchString'])){
            $allParams['searchString'] = str_replace(array('.', '-', '/'), '', $allParams['searchString']);
        }
        $paginator = $this->_bo->paginator($allParams);
        //$this->view->listIdFailTotal    = $this->_bo->getIdFailTotal();
        $this->view->paginator          = $paginator;
    }

    public function _initForm()
    {
        $unidadeBo = new Sis_Model_Bo_TipoUnidade();
        $marcaBo   = new Material_Model_Bo_Marca();
        $empresaBo = new Empresa_Model_Bo_Empresa();

        $this->view->freteCombo = array(
                Material_Model_Bo_Nfe::SEM_FRETE       => 'Sem frete',
                Material_Model_Bo_Nfe::EMITENTE        => 'Emitente',
                Material_Model_Bo_Nfe::DESTINATARIO    => 'Destinatário',
                Material_Model_Bo_Nfe::EMIT_DESTIN     => 'Emitente/Destinatário',
                Material_Model_Bo_Nfe::DEST_REMETE     => 'Destinário/Remetente'
        );

        $this->view->pessoalCombo     = array(null => '---- Selecione ----')+$empresaBo->getFuncionarioPairs(false);
        $this->view->unidadeCombo     = array(null => '---- Selecione ----')+$unidadeBo->getPairs(false);
    }

    public function formAction()
    {
        $this->_initForm();
        $impostoBo       = new Material_Model_Bo_Imposto();
        $transpBo        = new Material_Model_Bo_Transportador();

        $this->_id       = $this->getParam('id_nfe');
        $request         = $this->getAllParams();
        $object          = $this->_bo->get($this->_id);
        $imposto         = $impostoBo->get($this->getParam('id_imposto'));
        $transportador   = $transpBo->get($this->getParam('id_transportador'));

        //Checando se registro possui workspace, se possuir ele valida se está no workspace correto, caso contrário redireciona para a action _redirectUnauthorizedWorkspace.
        if(isset($object->id_workspace) && !empty($object->id_workspace)){

            $workspaceBO 	= new Auth_Model_Bo_Workspace();

            if (!$workspaceBO->validateRegisterWithWorkspace($object->id_workspace)){
                $this->redirect("/{$this->getRequest()->getModuleName()}/{$this->getRequest()->getControllerName()}/{$this->_redirectUnauthorizedWorkspace}");
            }

        }

        // verificar se vem via post
        if($this->getRequest()->isPost()){
            try {
                $impostoBo->saveFromRequest($request, $imposto);
                $request['id_imposto'] = $imposto->id_imposto;
                if(!empty($request['id_transp_empresa'])){
                    $transpBo->saveFromRequest($request, $transportador);
                    $request['id_transportador'] = $transportador->id_transportador;
                }



                $this->_bo->saveFromRequest($request, $object);
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array( 'success' => true, 'id_nfe' => $object->id_nfe, 'id_imposto' => $object->id_imposto, 'id_transportador' => $object->id_transportador);
                    $this->_helper->json($response);
                }

                if(empty($this->_messageFormSuccess)){
                    App_Validate_MessageBroker::addSuccessMessage("Dado inserido com sucesso");
                }else{
                    App_Validate_MessageBroker::addSuccessMessage($this->_messageFormSuccess);
                }

                if($this->_redirectFormSuccess ){
                    $this->_redirect('/material/nfe/form/id_nfe/'.$object->id_nfe);
                }
            }
            catch (App_Validate_Exception $e){
                //verifica se e pelo ajax
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array('success' => false , 'mensagem' => $this->_mensagemJson());
                    $this->_helper->json($response);
                }
            }
            catch (Exception $e){
                //verifica se e pelo ajax
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array('success' => false, 'message'=>'Errado '.$e->getMessage() );
                    $this->_helper->json($response);
                }
                App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro inesperado. Entre em contato com o administrador.".$e->getMessage());
            }
        }

        $this->view->vo = $object;
    }


    public function gridItemAction()
    {
        $this->_helper->layout->disableLayout();
        $id_nfe             = $this->getParam('id_nfe');
        $estoqueBo          = new Material_Model_Bo_Estoque();
        $movBo              = new Material_Model_Bo_Movimento();
        $listItem           = array();
        $listItemMov        = array();

        if(!empty($id_nfe)){

            $criteria = array('id_nfe = ?' => $id_nfe );
            $listItemMov = $movBo->find($criteria);

        }

        $this->view->listItemMov  = $listItemMov;
    }
}