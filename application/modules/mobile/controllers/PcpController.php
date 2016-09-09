<?php

class Mobile_PcpController extends App_Controller_Action_AbstractCrud
{

    public function preDispatch()
    {
    }
    public function init()
    {
    	$this->_messageBroker = App_Validate_MessageBroker::getInstance();
    	header('Access-Control-Allow-Origin: *');
    }
    
    public function setTimerAction()
    {
    	
    	$this->noRenderAndNoLayout();
    	
    		$request = $this->getAllParams();
    		
    			$empresaBo = new Empresa_Model_Bo_Empresa();
    			$usuarioBo = new Auth_Model_Bo_Usuario();
    			 
    			$deviceBo = new Mobile_Model_Bo_Device;
    			 
    			$uuidValid = $deviceBo->checkValidDevice($this->getParam("uuid"));
    			 
    			if ($uuidValid == false){
    				 
    				$this->_helper->json(array("success" => false, "mensagem" => "Aparelho não autorizado" ));
    				exit;
    				 
    			}
    			
    			$empresaObj = $empresaBo->get($this->getParam("empresas_id"));
    			 
    			if (count($empresaObj)>0){
    				
    				$empresaBo = new Empresa_Model_Bo_Empresa();
   					$timerBo = new Pcp_Model_Bo_Timer();
   					
   					if (isset($request['id_timer'])){
    						
    					$object = $timerBo->get($request['id_timer']);
    					$object->fim_work = date("Y-m-d H:i:s");
    						
    				}else{
    					
    					$timerRunProcessoByFuncionario = $timerBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO,"fim_work is null", "empresas_id = ?" => $this->getParam("empresas_id")))->toArray();

    					if (count($timerRunProcessoByFuncionario)>0){
    						$this->_helper->json(array("success" => false, "mensagem" => "Já existe trabalhos sendo executados para este funcionário"));
    						exit;
    					}
    					
    					$object = $timerBo->get();
    					$object->inicio_work = date("Y-m-d H:i:s");
    					$object->pro_id = $request['processoSelect'];
    					$object->empresas_id = $empresaObj->id;
    				}
    						
    				try {
   						$timerBo->saveFromRequest($request, $object);
   						$response = array( 'success' => true, 'id' => $object->id_timer);
   						$this->_helper->json($response);
    						
    					}
    				catch (App_Validate_Exception $e){
    					//verifica se e pelo ajax
    					if($this->getRequest()->isXmlHttpRequest()){
    						$response = array('success' => false , 'mensagem' => $this->_mensagemJson());
    						$this->_helper->json($response);
    					}

    					$response = array('success' => false , 'mensagem' => $this->_mensagemJson());
    					$this->_helper->json($response);
    						
    				}
    				catch (Exception $e){
    					//verifica se e pelo ajax
    					if($this->getRequest()->isXmlHttpRequest()){
    						$response = array('success' => false, 'mensagem'=>'Error: '.$e->getMessage() );
    						$this->_helper->json($response);
    					}
    				}
    			 
	    		}else{
	    			$this->_helper->json(array("success" => false, "mensagem" => "Usuário não encontrado" ));
	    		}
    }
    public function getWorkspaceAction()
    {
    
    	$this->noRenderAndNoLayout();
    
    	/*if($this->getRequest()->isPost()){*/
    
    	$workspaceBo = new Auth_Model_Bo_Workspace();
    	
    	$deviceBo = new Mobile_Model_Bo_Device;
    	
    	$uuidValid = $deviceBo->checkValidDevice($this->getParam("uuid"));
    	
    	if ($uuidValid == false){
    		
    		$this->_helper->json(array("success" => "false", "message" => "Aparelho não autorizado" ));
    		exit;
    		
    	}else{
    		$workspaceList = $workspaceBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO))->toArray();
    		
    		if(count($workspaceList)>0){
    		
    			$response = array('success' => true,'workspacelist'=> $workspaceList);
    		
    		}else{
    		
    			$response = array('success' => false , "message" => "Nenhum workspace encontrado" );
    		}
    		
    		$this->_helper->json($response);
    	}
    
    	/*}else{
    	 $this->_helper->json(array("success" => "false", "message" => "Modo de envio violado" ));
    	}*/
        
    }
    
    public function getTimerListAction()
    {
    	$this->noRenderAndNoLayout();
    	
    	$processoBo = new Processo_Model_Bo_Processo();
    	$timerBo = new Pcp_Model_Bo_Timer();
    	$empresaBo = new Empresa_Model_Bo_Empresa();
    	$deviceBo = new Mobile_Model_Bo_Device;
    	
    	$uuidValid = $deviceBo->checkValidDevice($this->getParam("uuid"));
    	 
    	if ($uuidValid == false){
    	
    		$this->_helper->json(array("success" => false, "message" => "Aparelho não autorizado" ));
    		exit;
    	
    	}
    	
    	$timerList = $timerBo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO,"fim_work is null" ))->toArray();

    	$resultArray = array();
    	
    	if(count($timerList)>0){
    		
    		foreach ($timerList as $key => $timerArray) {
    			
    			$processoObj  = $processoBo->get($timerArray['pro_id']);
    			
    			if ($processoObj->id_workspace == $this->getParam('workspace')){

    				$empresaObj = $empresaBo->get($timerArray['empresas_id']);
    				$workedHours = $timerBo->getWorkedHours($timerArray['id_timer']);
    				
    				$timerArray['workedHours'] = $workedHours['worked_hours'];
    				$timerArray['pro_codigo'] = $processoObj->pro_codigo;
    				$timerArray['funcionario'] = $empresaObj->nome_razao;
    				
    				$resultArray[] = $timerArray;
    				
    			}
    			
    		}
    		
    	}
    	$response = array('success' => true,'timerlist'=> $resultArray);
    	
    	$this->_helper->json($response);
    }
    
    public function getFuncionarioAction()
    {
    	$this->noRenderAndNoLayout();
    	 
    	$empresaBo = new Empresa_Model_Bo_Empresa();
    	$deviceBo = new Mobile_Model_Bo_Device;
    	
    	$uuidValid = $deviceBo->checkValidDevice($this->getParam("uuid"));
    	
    	if ($uuidValid == false){
    		 
    		$this->_helper->json(array("success" => false, "message" => "Aparelho não autorizado" ));
    		exit;
    		 
    	}
    	 
    	
    	$funcionarioGetPairsList = $empresaBo->getFuncionarioPairs();
    	
    	if(count($funcionarioGetPairsList)>0){
    		
    		$response = array('success' => true,'funcionariosList'=> $funcionarioGetPairsList);
    		
    	}else{
    		
    		$response = array('success' => false,"message" => "Não foi encontrado funcionários" );
    	}

    	$this->_helper->json($response);
    	 
    }
    
    public function getProcessoListAction()
    {
    	$this->noRenderAndNoLayout();
    	
    	$processoBo = new Processo_Model_Bo_Processo();
    	
    	$deviceBo = new Mobile_Model_Bo_Device;
    	 
    	$uuidValid = $deviceBo->checkValidDevice($this->getParam("uuid"));
    	 
    	if ($uuidValid == false){
    	
    		$this->_helper->json(array("success" => false, "message" => "Aparelho não autorizado" ));
    		exit;
    	
    	}

    	$id_workspace = $this->getParam("workspace");
    	
    	if( $id_workspace != "" ){
    		
    		$processoList = $processoBo->find(array("id_workspace = ?" => $id_workspace));
    		
    	}else{
    		
    		$processoList = $processoBo->find();
    	}
    	
    	
    	$arrayProcesso = array();
    	
    	foreach ($processoList as $key => $processo) {
    		
    		$arrayProcesso[$processo->pro_id] = $processo->pro_codigo;
    		
    	}
    	
    	if(count($arrayProcesso)>0){
    
    		$response = array('success' => true,'processolist'=> $arrayProcesso);
    	}else{
    		$response = array('success' => false, "message" => "Não há processos neste workspace" );
    	}
    	 
    	$this->_helper->json($response);
    }
    
}

