<?php
/**
 * @author Vinicius Leônidas
 * @since 24/11/2013
 */
class Rh_Model_Dao_FolhaDePagamento extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_folha_de_pagamento';
	protected $_primary = 'id_rh_folha_de_pagamento';
	protected $_namePairs = 'id_rh_folha_de_pagamento';
	
	protected $_rowClass	  = "Rh_Model_Vo_FolhaDePagamento";
	
	protected $_referenceMap    = array(
	
			'PlanoContas' => array(
					'columns'           => 'plc_id',
					'refTableClass'     => 'Financial_Model_Dao_PlanoContas',
					'refColumns'        => 'plc_id'
			),
			'Moeda' => array(
					'columns'           => 'moe_id',
					'refTableClass'     => 'Financial_Model_Dao_Moeda',
					'refColumns'        => 'moe_id'
			),
			'CentroCusto' => array(
					'columns'           => 'cec_id',
					'refTableClass'     => 'Financial_Model_Dao_CentroCusto',
					'refColumns'        => 'cec_id'
			),
			'TipoMovimento' => array(
					'columns'           => 'tmv_id',
					'refTableClass'     => 'Financial_Model_Dao_TipoMovimento',
					'refColumns'        => 'tmv_id'
			),
			'Empresa' => array(
					'columns'           => 'id_empresa',
					'refTableClass'     => 'Empresa_Model_Dao_Empresa',
					'refColumns'        => 'id'
			),
			'TipoPagamento' => array(
					'columns' 			=> 'id_tp_pagamento',
					'refTableClass'		=> 'Rh_Model_Dao_TipoPagamento',
					'refColumns'		=> 'id_tp_pagamento'
			)
	);
}