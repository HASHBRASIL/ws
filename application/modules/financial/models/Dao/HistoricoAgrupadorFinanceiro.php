<?php
/**
 * @author Vinicius Silva Pinto Leônidas
 * @since  18/03/2014
 */
class Financial_Model_Dao_HistoricoAgrupadorFinanceiro extends App_Model_Dao_Abstract
{
	protected $_name          = "fin_th_agrupador_financeiro";
	protected $_primary       = "id_th_agrupador_financeiro";
	protected $_rowClass	  = "Financial_Model_Vo_AgrupadorFinanceiro";
	protected $_namePairs	  = 'id_agrupador_financeiro';

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
        'EmpresaCliente' => array(
            'columns'           => 'id_pessoa_cliente',
            'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
            'refColumns'        => 'id'
        ),'Grupo' => array(
            'columns'           => 'id_grupo',
            'refTableClass'     => 'Legacy_Model_Dao_Grupo',
            'refColumns'        => 'id'
        ),
			'Processo' => array(
					'columns'           => 'pro_id',
					'refTableClass'     => 'Processo_Model_Dao_Processo',
					'refColumns'        => 'pro_id'
            ),
        'Usuario' => array(
            'columns'           => 'id_criacao_usuario',
            'refTableClass'     => 'Legacy_Model_Dao_Pessoa',
            'refColumns'        => 'id'
        ),
	);


}