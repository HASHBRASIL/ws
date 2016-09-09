<?php
class Service_TipoUnidadeController extends App_Controller_Action_AbstractCrud
{
    /**
     * @var Service_Model_Bo_TipoUnidade
     */
    protected $_bo;

    public function init()
    {
        $this->_bo = new Service_Model_Bo_TipoUnidade();
        parent::init();
    }

    public function _initForm()
    {
        $this->_id = $this->getParam('id_tipo_unidade');
    }
}