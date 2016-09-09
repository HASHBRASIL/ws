<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_ClasseController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Material_Model_Bo_Classe
     */
    protected $_bo;

    public function init()
    {
         $this->_bo = new Material_Model_Bo_Classe();
         $this->_aclActionAnonymous = array('pairs');
         parent::init();
         $this->_redirectDelete = '/material/grupo/index';
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
            $id_classe = $this->getParam('id_classe');
            $request     = $this->getAllParams();
            $objectClasse = $this->_bo->get($id_classe);

            try {
                $grupo = $this->_bo->saveFromRequest($request, $objectClasse);
                $response = array('success' => true, 'id' => $objectClasse->id_classe, 'nome' => $objectClasse->nome);
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

    public function pairsAction()
    {
        $idSubgrupo = $this->getParam('id_subgrupo');
        $criteria = array('id_subgrupo = ?' => $idSubgrupo, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO);
        $listClasse = $this->_bo->getPairsSubgrupo($criteria);
        $this->_helper->json(array('list' => $listClasse));
    }

}