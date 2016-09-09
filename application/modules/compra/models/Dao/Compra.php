<?php
/**
 * @author Vinicius Leonidas
 * @since 21/10/2013
 */
class Compra_Model_Dao_Compra extends App_Model_Dao_Abstract
{
	
	protected $_name          = "tb_co_compra";
	protected $_primary       = "id_compra";
	
	protected $_rowClass = 'Compra_Model_Vo_Compra';
	
	protected $_referenceMap    = array(
	
			'Corporativo' => array(
					'columns'           => 'id_consultor',
					'refTableClass'     => 'Empresa_Model_Dao_Empresa',
					'refColumns'        => 'id'
			),
			'Campanha' => array(
					'columns'           => 'id_campanha',
					'refTableClass'     => 'Compra_Model_Dao_Campanha',
					'refColumns'        => 'id_campanha'
			));

}