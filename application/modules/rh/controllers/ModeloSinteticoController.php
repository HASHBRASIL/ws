<?php
/**
 * @author Vinicius S P Leônidas
 * @since 03/12/2013
 */
class Rh_ModeloSinteticoController extends App_Controller_Action_AbstractCrud
{
	
	protected $_bo;
	protected $_redirectDelete = 'rh/modelo-sintetico/grid';
	
	public function init(){
		$this->_helper->layout()->setLayout('metronic');
		$this->_bo = new Rh_Model_Bo_ModeloSintetico();
		$this->_aclActionAnonymous = array('autocomplete');
		parent::init();
		$this->_id = $this->getParam("id_rh_modelo_sintetico");
	} 
	
	public function gridAction(){
		$this->view->iten = $this->_bo->find(array("ativo = ?" => App_Model_Dao_Abstract::ATIVO));
	}
	public function _initForm(){
		
		$entradaBo = new Rh_Model_Bo_EntradaSintetico();
		$naturezaBo = new Rh_Model_Bo_NaturezaSintetico();
		
		$this->view->comboEntrada		= $entradaBo->getPairs();
		$this->view->comboNatureza = $naturezaBo->getPairs();
	}
	
	public function validarCamposAjaxAction(){
	
		$data = $this->_getParam('id_rh_modelo_sintetico');
	
		$this->_helper->json($this->_bo->verificarModelo($data));
	
	}
}
