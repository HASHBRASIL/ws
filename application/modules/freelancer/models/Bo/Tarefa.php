<?php
/**
* @author Carlos Vinicius Bonfim da Silva
* @since  12/02/2015
*/
class Freelancer_Model_Bo_Tarefa extends App_Model_Bo_Abstract
{
  /**
  * @var Freelancer_Model_Dao_Tarefa
  */
  protected $_dao;

  /**
  * @var integer
  */
  public function __construct()
  {
    $this->_dao = new Freelancer_Model_Dao_Tarefa();
    parent::__construct();
  }

  protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
  {
    
    if ($object->dt_inicio != ""){
      $dt_inicio = new Zend_Date($object->dt_inicio);
      $object->dt_inicio = $dt_inicio->toString('yyyy/MM/dd HH:mm');
    }
    if ($object->dt_fim != ""){
      $dt_fim = new Zend_Date($object->dt_fim);
      $object->dt_fim = $dt_fim->toString('yyyy/MM/dd HH:mm');
    }

    if (empty($object->id_workspace)){
      $workspaceSession = new Zend_Session_Namespace('workspace');
      $object->id_workspace = $workspaceSession->id_workspace;
    }

  }

  protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
  {
    if(empty($object->id_empresa)){
      App_Validate_MessageBroker::addErrorMessage('O campo de freelancer está vazio.');
      return false;
    }
    if(empty($object->dt_inicio)){
      App_Validate_MessageBroker::addErrorMessage('O campo da data do início da tarefa de freelance está vazio.');
      return false;
    }
    if(empty($object->dt_fim)){
      App_Validate_MessageBroker::addErrorMessage('O campo da data estimada para o término do freelance está vazio.');
      return false;
    }
    if(empty($object->percentual_completado)){
      $object->percentual_completado = 0;
    }
    if(empty($object->descricao)){
      App_Validate_MessageBroker::addErrorMessage('O campo de descrição está vazio.');
      return false;
    }

  return true;
}

}
