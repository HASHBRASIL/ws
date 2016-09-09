<?php
class Service_TarefaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Service_Model_Bo_Tarefa
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_Tarefa();
        parent::init();
    }

    public function gridAction()
    {
        //Desativar o layout
        $this->_helper->layout->disableLayout();

        $servicoBo = new Service_Model_Bo_Servico();
        $idServico = $this->getParam('id_servico');
        if(empty($idServico)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um serviço.");
            $this->redirect('service/group/index/');
        }

        $this->view->servico    = $servicoBo->get($idServico);
        $this->view->tarefaList = $this->_bo->getListTarefa($idServico);
    }

    public function indexAction()
    {
        $servicoBo = new Service_Model_Bo_Servico();
        $idServico = $this->getParam('id_servico');
        if(empty($idServico)){
            App_Validate_MessageBroker::addErrorMessage("Selecione um serviço.");
            $this->redirect('service/group/index');
        }

        $this->view->servico    = $servicoBo->get($idServico);
    }

    public function formAction()
    {
        // verificar se vem via post
        if($this->getRequest()->isPost()){
            $id_tarefa = $this->getParam('id_tarefa');
            $request     = $this->getAllParams();
            $objectTarefa = $this->_bo->get($id_tarefa);

            try {
                $grupo = $this->_bo->saveFromRequest($request, $objectTarefa);
                $response = array('success' => true);
                $this->_helper->json($response);
            }
            catch (App_Validate_Exception $e){
                //verifica se e pelo ajax
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array('success' => false);
                    $this->_helper->json($response);
                }
            }
            catch (Exception $e){
                $response = array('success' => false, 'message'=>'Errado '.$e->getMessage() );
                $this->_helper->json($response);
            }
        }
    }

    /**
     * @desc irá inativar o dado apartir do params recebido pelo request com a chave id
     */
    public function deleteAllAction()
    {

        //Desativar a view
        $this->_helper->viewRenderer->setNoRender();

        //verifica se e pelo ajax
        if($this->getRequest()->isXmlHttpRequest()){
            $idServico = $this->getParam('id_servico');
            try {
                $this->_bo->inativarAll($idServico);
                $response = array('success' => true);
            } catch (Exception $e) {
                App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro inesperado.");
                $response = $this->_mensagemJson();
            }



            $this->_helper->json($response);
        }
    }

}