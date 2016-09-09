<?php

class Config_ConfiguracaoController extends App_Controller_Action
{
    /**
     * @var Zend_Session_Namespace Armazena dados da validação da inscrição
     */
    protected $session;


 	public function init()
    {
        parent::init();

        $this->_translate = Zend_Registry::get('Zend_Translate');

        $this->_helper->layout()->setLayout('publico');
        //$this->session = new Zend_Session_Namespace('validacao');
    }

    /**
     * configuração padrão para definir o uso de twig em todas as actions do controller
     * @todo pode-se criar um App_controller_Action_Twig para facilitar isso
     */
    public function postDispatch()
    {
        $this->view->params = $this->getAllParams();
        $this->renderScript('twig.phtml');
        return parent::postDispatch();
    }

    public function indexAction()
    {
        $this->view->data = [
        				'teste' => 'teste abri a variavel'
        ];
        $this->view->file = 'index.html.twig';
    }

    public function editperfilAction()
    {
    	$this->view->data = [
    					'teste' => 'teste ronaldo'
    	];
    	$this->view->file = 'form.html.twig';
    }
}
