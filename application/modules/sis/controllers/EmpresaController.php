<?php
class Sis_EmpresaController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_Empresa
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Sis_Model_Bo_Empresa();
        parent::init();
    }

    public function fornecedorJsonAction()
    {
        $fornecedorBo     = new Sis_Model_Bo_Fornecedor();
        $this->_AutocompleteJson($fornecedorBo);
    }

    public function grupoJsonAction()
    {
        $empresaGrupoBo = new Sis_Model_Bo_EmpresaGrupo();
        $this->_AutocompleteJson($empresaGrupoBo);
    }

    public function geralJsonAction()
    {
        $this->_AutocompleteJson($this->_bo);
    }

    private function _AutocompleteJson($object)
    {
        $term = $this->getRequest()->getParam('term');
        $list = $object->getAutocomplete($term);
        $this->_helper->json($list);
    }

}