<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  07/10/2013
 */
class Material_ItemOpcaoController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Material_Model_Bo_ItemOpcao
     */
    protected $_bo;

    public function init()
    {
        $this->_helper->layout()->setLayout('metronic');
        $this->_bo = new Material_Model_Bo_ItemOpcao();
        parent::init();
        $this->_aclActionAnonymous = array('get-by-atributo','get-by-item');

    }

    public function deleteAction()
    {
        $idItem         = $this->getParam('id_item');
        $idAtributo     = $this->getParam('id_atributo');

        //verifica se e pelo ajax
        if($this->getRequest()->isXmlHttpRequest()){
            if($this->_bo->deleteByAtributo($idAtributo, $idItem)){
                App_Validate_MessageBroker::addSuccessMessage("Registro removido com sucesso.");
            }else {
                App_Validate_MessageBroker::addErrorMessage('Não foi possivel apagar este registro');
            }


            $response = $this->_mensagemJson();
            $this->_helper->json($response);
        } else {
            if($this->_bo->deleteByAtributo($idAtributo, $idItem)){
                App_Validate_MessageBroker::addSuccessMessage("Registro removido com sucesso.");
            }else {
                App_Validate_MessageBroker::addErrorMessage('Não foi possivel apagar este registro');
            }
            if(!$this->_redirectDelete){
                $this->_helper->redirector->gotoSimple( 'index',
                                                        $this->getRequest()->getControllerName(),
                                                        $this->getRequest()->getModuleName());
            } else {
                $this->_redirect($this->_redirectDelete);
            }
        }
    }

    public function getByAtributoAction()
    {
        $idAtributo = $this->getParam('id_atributo');
        $idItem     = $this->getParam('id_item');
        $opcaoList = $this->_bo->findOpcao($idItem, $idAtributo);
        $this->_helper->json($opcaoList);
    }

    public function getByItemAction()
    {
        $idItem = $this->getParam('id_item');

        $opcaoList = $this->_bo->findOpcao($idItem);

        $this->_helper->json(array('list' => $opcaoList, 'count'=>count($opcaoList)));
    }
}