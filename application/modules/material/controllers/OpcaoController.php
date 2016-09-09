<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  26/09/2013
 */
class Material_OpcaoController extends App_Controller_Action_AbstractCrud
{
    /**
     *
     * @var Material_Model_Bo_Opcao
     */
    protected $_bo;

    public function init()
    {
    	$this->_helper->layout()->setLayout('metronic');
        $this->_bo = new Material_Model_Bo_Opcao();
        parent::init();
        $this->_id = $this->getParam('id_opcao');
        $this->_aclActionAnonymous = array('get', 'get-pairs-by-atributo');
    }

    public function getAction()
    {
        $idOpcao = $this->getParam('id_opcao');
        $opcaoList = $this->_bo->get($idOpcao);
        $this->_helper->json($opcaoList->toArray());
    }

    public function getPairsByAtributoAction()
    {
        $id_atributo = $this->getParam('id_atributo');
        $opcao = $this->_bo-> getPairsByAtributo($id_atributo);
        $this->_helper->json($opcao);
    }

}