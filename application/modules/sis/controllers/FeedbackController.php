<?php
/**
 * @author Vinícius S P Leônidas
 * @since  11/10/2013
 */
class Sis_FeedbackController extends App_Controller_Action_AbstractCrud
{
	protected $_bo;

	protected $_redirectDelete = 'sis/feedback/grid';

	public function init()
	{
		$this->_bo = new Sis_Model_Bo_Feedback();
		$this->_aclActionAnonymous = array('form', 'assunto-feed', 'grid', 'delete');
    $this->_helper->layout()->setLayout('metronic');

		parent::init();
	}

	public function assuntoFeedAction(){

		$tipoFeedBo =  new Sis_Model_Bo_TipoFeedback();

		$dados = $tipoFeedBo->getPairs()+array('0' => '---- Selecione ----');

		$this->_helper->json($dados);

	}

	public function gridAction()
	{
		$this->view->feedList = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO), array('id_status_feed ASC','dt_criacao ASC'));

	}

}
