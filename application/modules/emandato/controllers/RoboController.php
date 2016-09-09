<?php
/**
 * @author Fernando Augusto
 * @since  17/05/2016
 */
class Emandato_RoboController extends Zend_Controller_Action
{

    protected $_bo;

    public function init()
    {
        parent::init();
        $this->_bo = new Emandato_Model_Bo_Robo();
    }

    public function noticiacamaraAction() {

        echo "Iniciando carga de noticias\n";

        $this->_bo->carregaNoticiasCamara();

        exit;

    }

}