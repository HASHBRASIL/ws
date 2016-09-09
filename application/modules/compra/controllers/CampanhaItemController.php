<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  17/10/2013
 */
class Compra_CampanhaItemController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Compra_Model_Bo_CampanhaItem
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Compra_Model_Bo_CampanhaItem();
        $this->_helper->layout()->setLayout('metronic');
        parent::init();
        $this->_id = $this->getParam('id_campanha_item');
        $this->_aclActionAnonymous = array('get');
    }

    public function formByCampanhaAction()
    {
        $this->_initForm();
        $request         = $this->getAllParams();
        // verificar se vem via post
        if($this->getRequest()->isPost()){
            try {
                $this->_bo->saveFromRequestByCampanha($request);
                if($this->getRequest()->isXmlHttpRequest()){
                    $response = array( 'success' => true);
                    $this->_helper->json($response);
                }

                if(empty($this->_messageFormSuccess)){
                    if(empty($this->_id)){
                        App_Validate_MessageBroker::addSuccessMessage("Dado inserido com sucesso");
                    }else{
                        App_Validate_MessageBroker::addSuccessMessage("Dado atualizado com sucesso");
                    }
                }else{
                    App_Validate_MessageBroker::addSuccessMessage($this->_messageFormSuccess);
                }

                if($this->_redirectFormSuccess){
                    $this->_redirect($this->_redirectFormSuccess);
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
                App_Validate_MessageBroker::addErrorMessage("Ocorreu um erro inesperado. Entre em contato com o administrador.");
            }
        }

    }

    public function gridCampanhaAction()
    {
        $idCampanha = $this->getParam('id_campanha');
        $this->_helper->layout()->disableLayout();

        $criteria = array(
                    'id_campanha = ?' => $idCampanha,
                    'ativo = ?' => App_Model_Dao_Abstract::ATIVO
                );
        $this->view->listCampanhaItem = $this->_bo->find($criteria);
    }

    public function getAction()
    {
        $idCampanhaItem = $this->getParam('id_campanha_item');
        $campanhaItem = $this->_bo->get($idCampanhaItem);
        $campanhaItemArray = $campanhaItem->toArray();
        $campanhaItemArray['nome_produto'] = $campanhaItem->getItem()->nome;
        $this->_helper->json($campanhaItemArray);
    }

}