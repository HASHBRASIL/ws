<?php
/**
 * @author Vinicius Silva Pinto Leônidas
 * @since  18/03/2014
 */
class Financial_Model_Dao_HistoricoFinanceiro extends App_Model_Dao_Abstract
{
	protected $_name          = "fin_th_financeiro";
	protected $_primary       = "id_th_financeiro";

	protected $_rowClass = 'Financial_Model_Vo_Financial';

	protected $_referenceMap    = array(
			'Status' => array(
					'columns'           => 'stf_id',
					'refTableClass'     => 'Financial_Model_Dao_Status',
					'refColumns'        => 'stf_id'
			),
			'Contas' => array(
					'columns'           => 'con_id',
					'refTableClass'     => 'Financial_Model_Dao_Contas',
					'refColumns'        => 'con_id'
			),
			'Moeda' => array(
					'columns'           => 'moe_id',
					'refTableClass'     => 'Financial_Model_Dao_Moeda',
					'refColumns'        => 'moe_id'
			),
			'Conta' => array(
					'columns'           => 'con_id',
					'refTableClass'     => 'Financial_Model_Dao_Contas',
					'refColumns'        => 'con_id'
			),
			'DocumentoInterno' => array(
					'columns'           => 'tid_id',
					'refTableClass'     => 'Financial_Model_Dao_DocumentoInterno',
					'refColumns'        => 'tid_id'
			),
			'DocumentoExterno' => array(
					'columns'           => 'tie_id',
					'refTableClass'     => 'Financial_Model_Dao_DocumentoExterno',
					'refColumns'        => 'tie_id'
			),
			'ModeloSintetico' => array(
					'columns'						=> 'fin_id',
					'refTableClass'			=> 'Rh_Model_Dao_ReferenciaFinanceiroModelo',
					'refColumns'				=> 'fin_id'
			),
            'Usuario' => array(
                    'columns'           => 'id_criacao_usuario',
                    'refTableClass'     => 'Auth_Model_Dao_Usuario',
                    'refColumns'        => 'usu_id'
            )
	);
}