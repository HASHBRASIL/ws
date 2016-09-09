<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  08/04/2013
 */
class Material_GrupoController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Material_Model_Bo_Grupo
     */
    protected $_bo;

    public function init()
    {
         $this->_helper->layout()->setLayout('metronic');
         $this->_bo = new Material_Model_Bo_Grupo();
         $this->_aclActionAnonymous = array('pairs');
         parent::init();
    }

    public function indexAction()
    {
        $workspaceSession      = new Zend_Session_Namespace('workspace');
        $itemBo                = new Material_Model_Bo_Item();
        $criteria              = array('ativo = ?' => App_Model_Dao_Abstract::ATIVO);

        $this->view->listGrupo            = $this->_bo->find($criteria);
        $this->view->idGrupoExist         = $itemBo->getIdGrupo();
        $this->view->idSubgrupoExist      = $itemBo->getIdSubgrupo();
    }

    public function formAction()
    {
        // verificar se vem via post
        if($this->getRequest()->isPost()){
            $id_grupo = $this->getParam('id_grupo');
            $request     = $this->getAllParams();
            $objectGrupo = $this->_bo->get($id_grupo);

            try {
                $grupo = $this->_bo->saveFromRequest($request, $objectGrupo);
                $response = array('success' => true, 'id' => $objectGrupo->id_grupo, 'nome' => $objectGrupo->nome);
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