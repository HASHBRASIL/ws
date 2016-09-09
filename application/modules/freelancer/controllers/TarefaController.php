<?php

class Freelancer_TarefaController extends App_Controller_Action_AbstractCrud
{
  /**
   * @var Freelancer_Model_Bo_Tarefa
   */
  protected $_bo;
  protected $_redirectDelete = "/freelancer/tarefa/grid";

  public function init()
  {
    $this->_helper->layout()->setLayout('metronic');
    $this->_bo = new Freelancer_Model_Bo_Tarefa();
    parent::init();
    $this->_id = $this->getParam("id_tarefa");
  }

  public function _initForm(){

    //$empresaBo = new Empresa_Model_Bo_Empresa();
    //$this->view->comboFuncionarios		= $empresaBo->getFuncionarioPairs();

  }

  public function gridAction(){

    $workspaceSession = new Zend_Session_Namespace('workspace');

    if ($workspaceSession->free_access){

      $return = $this->_bo->find(array("ativo = ?" =>App_Model_Dao_Abstract::ATIVO));

    }else{

      $return = $this->_bo->find("id_workspace = {$workspaceSession->id_workspace} or id_workspace IS NULL and ativo =".App_Model_Dao_Abstract::ATIVO);
    }

    $this->view->freelancerList = $return;

  }

}
