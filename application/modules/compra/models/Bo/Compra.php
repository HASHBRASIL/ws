<?php
/**
 * @author Vinicius Leônidas
 * @since 21/10/2013
 */
class Compra_Model_Bo_Compra extends App_Model_Bo_Abstract
{
	/**
	 * @var Compra_Model_Dao_Compra
	 */
	protected $_dao;

	public function __construct()
	{
		$this->_dao = new Compra_Model_Dao_Compra();
		parent::__construct();
	}
	    
	protected function _validar($object, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if ($object->finalizado == App_Model_Dao_Abstract::ATIVO){
			
			$campanhaBo = new Compra_Model_Bo_Campanha();
			$campanhaCorporativoBo = new Compra_Model_Bo_CampanhaCorporativo();
			
			$compra = $this->_dao->find(array(
					'id_consultor = ?' => Zend_Auth::getInstance()->getIdentity()->id,
					'id_compra = ?' => $object->id_compra,
					'ativo = ?' => App_Model_Dao_Abstract::ATIVO,
					'finalizado = ?' => App_Model_Dao_Abstract::ATIVO
			))->current();
			
			$campanha = $campanhaBo->find('id_campanha = '. $object->id_campanha)->current();
			
			$corporativo = $campanhaCorporativoBo->find(array(
					'id_corporativa = ?' => Zend_Auth::getInstance()->getIdentity()->id,
					'id_campanha = ?' => $object->id_campanha
			))->current();
			$numeroCompra = $this->_dao->fetchAll(array(
					'id_consultor = ?' => Zend_Auth::getInstance()->getIdentity()->id,
					'id_campanha = ?' => $object->id_campanha,
					'ativo = ?' => App_Model_Dao_Abstract::ATIVO
			))->count();
			
			if ($corporativo->vl_max_compra == null || $corporativo->vl_max_compra == 0.00){ 
				$vlMaxCompra = $campanha['vl_max_compra'];
			} else {
				$vlMaxCompra = $corporativo['vl_max_compra'];
			}
			
			if($object->total >= $campanha->vl_min_compra){
					
			} else {
				//mensagem do erro
				App_Validate_MessageBroker::addErrorMessage("Você não tem o valor minimo para compra.");
				return false;
			}
			
			if($object->total <= $vlMaxCompra){
				
			} else {
				//mensagem do erro
				App_Validate_MessageBroker::addErrorMessage("Você excedeu o valor maximo da compra.");
				return false;
			}
			
			$maxCompraCampanha = $corporativo->qtd_compra ? $corporativo->qtd_compra : $campanha->qtd_compra;
			
			if($numeroCompra <= $maxCompraCampanha){
					
			} else {
				//mensagem do erro
				App_Validate_MessageBroker::addErrorMessage("Você não pode mas fazer compra nessa campanha");	
				return false;
			}
		}

		App_Validate_MessageBroker::addSuccessMessage("Compra finalizada!");
		
		return true;
	}
	public function comparaItens($searchProduto, $listProduto){
		if (empty($listProduto)) {
			return false;
		}
		foreach ($listProduto as $key => $produto){
			if($searchProduto['id_item'] == $produto['id_item'] ){
				if(count($produto['id_opcao']) > 0){
					$return = false;
					foreach ($produto['id_opcao'] as $keyOpcao => $idOpcao){
						if($searchProduto['id_opcao'][$keyOpcao] == $idOpcao){
							$return = true;
						}else{
							$return = false;
							BREAK;
						}
					}
					if($return){
						return $key;
					}
				}else{
					return $key;
				}
			}
		}
		return false;
	}
	public function contarComprasRelaizadas($id){
		
		$campanhaBo = new Compra_Model_Bo_Campanha();

		$objCampanha = $campanhaBo->find(array("id_campanha = ?" => $id, 'ativo = ?' => App_Model_Dao_Abstract::ATIVO));
		$objCompra = $this->find(array(
				'id_campanha = ?' => $id,
				'id_consultor = ?' => Zend_Auth::getInstance()->getIdentity()->id,
				'ativo = ?' => App_Model_Dao_Abstract::ATIVO
		))->count();
		if ($objCampanha['']['qtd_compra'] <= $objCompra) {
			return false;
		}
		return true;
	}
}