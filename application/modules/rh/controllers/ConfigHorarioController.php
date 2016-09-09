<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 24/08/2014
 */
class Rh_ConfigHorarioController extends App_Controller_Action_AbstractCrud
{
	/**
	 * @var Rh_Model_Bo_ConfigHorario
	 */
	protected $_bo;
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_ConfigHorario();
		parent::init();
		$this->_id = $this->getParam('id_config_horario');
	}
	
	public function duplicarAction()
    {
    	//App_Util_Functions::debug($this->getAllParams());
        $this->_initForm();
        $request         = $this->getAllParams();
        
        // verificar se vem via post
        if($this->getRequest()->isPost()){
            try {
                $this->_bo->duplicarFromRequest($request);
                $response = array( 'success' => true);
                $this->_helper->json($response);

            }
            catch (App_Validate_Exception $e){
                $response = array('success' => false , 'mensagem' => $this->_mensagemJson());
                $this->_helper->json($response);
            }
            catch (Exception $e){
                $response = array('success' => false, 'message'=>'Errado '.$e->getMessage() );
                $this->_helper->json($response);
            }
        }

    }
}