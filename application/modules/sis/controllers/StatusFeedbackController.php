<?php
/**
 * @author Vinícius S P Leônidas
 * @since  11/10/2013
 */
class Sis_StatusFeedbackController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Sis_Model_Bo_StatusFeedback
     */
	protected $_bo;


	public function init()
	{
		$this->_bo = new Sis_Model_Bo_StatusFeedback();
		$this->_aclActionAnonymous = array('autocomplete');
    $this->_helper->layout()->setLayout('metronic');

		parent::init();
	}

    public function autocompleteAction()
    {
        $term = $this->getRequest()->getParam('term');
        $list = $this->_bo->getAutocomplete($term, false);
        $this->_helper->json($list);
    }

    public function getAction()
    {
        $idStatus = $this->getParam('id');
        $statusList = $this->_bo->get($idStatus);

        $this->_helper->json($statusList);
    }
}
