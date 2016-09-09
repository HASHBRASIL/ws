<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  02/5/2013
 */
class Empresa_GrupoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Empresa_Model_Bo_EmpresaGrupo
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Empresa_Model_Bo_EmpresaGrupo();
        parent::init();
    }

    public function autocompleteAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocomplete($term);
        $this->_helper->json($list);
    }

    public function getAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $id = $this->getParam('id');
        $empresa = $this->_bo->get($id);
        $empresaJson = array();
        foreach ($empresa as $key => $value){
            $empresaJson[$key] = $value;
        }
        $this->_helper->json($empresaJson);
    }

}