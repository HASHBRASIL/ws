<?php
/**
 * @author Vinicius Leônidas
* @since 03/12/2013
*/
class Rh_Model_Bo_ReferenciaFinanceiroModelo extends App_Model_Bo_Abstract
{
	protected $_dao;

	public function __construct(){
		$this->_dao = new Rh_Model_Dao_ReferenciaFinanceiroModelo();
		parent::__construct();
	}
	public function delReferencia($id){
		return $this->_dao->delReferencia($id);
	}
	public function totalProventoAndDesconto($tipo, $dataCompetencia = null){
		$data = new Zend_Date($dataCompetencia);
		$data = $data->toString('yyyy-MM-dd');
		return $this->_dao->totalProventoAndDesconto($tipo, $data);
	}
}