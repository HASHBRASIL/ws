<?php
/**
 * @author Vinicius Leônidas
 * @since 23/10/2013
 */
class Compra_Model_Bo_CompraItem extends App_Model_Bo_Abstract
{
	/**
	 * @var Compra_Model_Dao_CompraItem
	 */
	protected $_dao;

	public function __construct()
	{
		$this->_dao = new Compra_Model_Dao_CompraItem();
		parent::__construct();
	}

	public function getItensList($id_campanha = null	){
		return $this->_dao->getItensList($id_campanha = null);
	}
	protected function _preSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
	{
		if (!empty($object->id_compra)) {
			$compraBo = new Compra_Model_Bo_Compra();
			$compraObj = $compraBo->get($object->id_compra);
			$compraObj->finalizado = 0; //*O 0 coloca em processo*/
			$compraObj->save();
		}
	}
 	protected function _postSave($object, $request, Zend_File_Transfer_Adapter_Http $uploadAdapter = null)
 	{
 		$compraOpcaoBo = new Compra_Model_Bo_CompraItemOpcao();
 		if (isset($request["atributos"])) {
 			$compraOpcaoBo->delOpcao($object->id_compra_item);
 			foreach ($request["atributos"] as $opcao){
 				$compraOpcao = $compraOpcaoBo->get();
 				$compraOpcao->id_compra_item = $object->id_compra_item;
 				$compraOpcao->id_opcao = $opcao;
 				$compraOpcao->save();
 			}
 		}
 	}

 	public function findProdutoComprado($id_campanha)
 	{
 	    $listItemComprado = $this->_dao->getItensList($id_campanha);
 	    $compraItemOpcaoBo = new Compra_Model_Bo_CompraItemOpcao();
 	    foreach ($listItemComprado as &$itemComprado){
 	        $itemComprado['id_opcao'] = $compraItemOpcaoBo->findOpcaoByCompra($itemComprado['id_compra_item']);
		      $itemComprado['nome_opcao'] = $compraItemOpcaoBo->findNomeOpcaoByCompra($itemComprado['id_compra_item']);	
 	    }
 	    
 	    return $listItemComprado;
 	}
}