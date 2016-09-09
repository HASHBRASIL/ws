<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/08/2013
 */
class Processo_MaterialProcessoController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Processo_Model_Bo_MaterialProcesso
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Processo_Model_Bo_MaterialProcesso();
        $this->_aclActionAnonymous = array('get');
        parent::init();
    }

    public function gridAction()
    {
        $this->_helper->layout()->disableLayout();
        $idProcesso = $this->getParam('pro_id');
        $criteria = array(
                'id_processo = ?' => $idProcesso,
                'ativo =?' => App_Model_Dao_Abstract::ATIVO
        );
        $this->view->materialList = $this->_bo->find($criteria);
    }

    public function getAction()
    {
        $id = $this->getParam('id');
        $materialProcesso = $this->_bo->get($id);
        $materialOpcaoBo = new Processo_Model_Bo_MaterialOpcao();

        $materialProcessoArr = $materialProcesso->toArray();
        $materialProcessoArr['nome_item'] = $materialProcesso->getItem()? $materialProcesso->getItem()->nome: $materialProcesso->nome;
        $atributo = $materialOpcaoBo->find(array('id_material_processo = ?'=>$id) );
        $materialProcessoArr['atributo'] = $atributo->toArray();

        $this->_helper->json($materialProcessoArr);
    }

    public function _initForm()
    {
        $this->_id = $this->getParam('id_material_processo');
    }

    public function validarAction()
    {
    	parent::formAction();
    }
}