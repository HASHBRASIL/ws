<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_CidadeController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_Cidade
     */
	
	protected $_aclActionAnonymous = array("pairs");
    protected $_bo;

    public function init()
    {
        $this->_bo = new Sis_Model_Bo_Cidade();
        parent::init();
    }

    public function pairsAction()
    {
        $idEstado = $this->getParam('id_estado');
        $criteria = array('ufs_id = ?' => $idEstado);
        $listCidade = $this->_bo->getPairsCidade($criteria);
        $this->_helper->json(array('list' => $listCidade));
    }
}