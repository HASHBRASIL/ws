<?php
/**
 * @author Vinicius Leônidas
* @since 23/10/2013
*/
class Compra_Model_Bo_CompraItemOpcao extends App_Model_Bo_Abstract
{
	/**
	 * @var Compra_Model_Dao_CompraItemOpcao
	 */
	protected $_dao;

	public function __construct()
	{
		$this->_dao = new Compra_Model_Dao_CompraItemOpcao();
		parent::__construct();
	}
	public function delOpcao($idCompra){

		return $this->_dao->delOpcao($idCompra);

	}

	public function getOpcao($idCompra){

		return $this->_dao->getOpcoes($idCompra);

	}

	public function getIdOpcao($idCompra)
	{
		return $this->_dao->getIdOpcoes($idCompra);
	}

	public function findOpcaoByCompra($id_compra_item)
	{
	    return $this->_dao->findOpcaoByCompra($id_compra_item);
	}

	public function findNomeOpcaoByCompra($id_compra_item){
		return $this->_dao->findNomeOpcaoByCompra($id_compra_item);
	}
}