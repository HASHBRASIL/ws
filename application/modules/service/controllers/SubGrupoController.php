<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/04/2013
 */
class Service_SubGrupoController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Service_Model_Bo_SubGrupo
     */
    protected $_bo;
    protected $_redirectDelete = 'service/grupo';

    public function init()
    {
         $this->_bo = new Service_Model_Bo_SubGrupo();
         parent::init();
    }

    public function indexAction()
    {
    }

    public function formAction()
    {
        //Desativar a view
        $this->_helper->viewRenderer->setNoRender();
        //Desativar o layout
        $this->_helper->layout->disableLayout();

        // verificar se vem via post
        if($this->getRequest()->isPost()){
            $id_subgrupo = $this->getParam('id_subgrupo');
            $request     = $this->getAllParams();
            $objectGrupo = $this->_bo->get($id_subgrupo);

            try {
                $grupo = $this->_bo->saveFromRequest($request, $objectGrupo);
                $response = array('success' => true, 'id' => $objectGrupo->id_subgrupo, 'nome' => $objectGrupo->nome);
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

}