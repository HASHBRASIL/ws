<?php
class Financial_DexionController extends App_Controller_Action_TwigCrud
{
    /**
     * @var Financial_Model_Bo_Dexion
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Financial_Model_Bo_Dexion();
        parent::init();
    }

    public function indexAction() {
        //x($this->identity->time['id']);

        //x($select->__toString());
        $filedir = Zend_Registry::getInstance()->get('config')->get('filedir');

        //$this->header = $this->_bo->fields;
        $this->view->fields = $this->_bo->fields;

        $this->_gridSelect = $this->_bo->getSelectDexion($this->identity->time['id']);

        //x($this->_gridSelect->__toString());

        $this->view->filedir = $filedir;

        parent::gridAction();

    }

    public function importAction() {
        $this->_bo->importFiles();
    }

    public function processAction() {
        $this->_bo->process();

    }

}