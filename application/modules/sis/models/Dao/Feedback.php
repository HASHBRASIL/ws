<?php
/**
 * @author Vinícius S P Leônidas
 * @since  11/10/2013
 */
class Sis_Model_Dao_Feedback extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_sis_feed';
	protected $_primary = 'id_feed';

	protected $_rowClass = 'Sis_Model_Vo_Feedback';

	protected $_referenceMap    = array(

			'TipoFeedback' => array(
					'columns'           => 'id_tipo_feed',
					'refTableClass'     => 'Sis_Model_Dao_TipoFeedback',
					'refColumns'        => 'id_tipo_feed'
			),
			'TipoCliente' => array(
					'columns'           => 'id_criacao_usuario',
					'refTableClass'     => 'Auth_Model_Dao_Usuario',
					'refColumns'        => 'usu_id'
			),
			'Status' => array(
					'columns'           => 'id_status_feed',
					'refTableClass'     => 'Sis_Model_Dao_StatusFeedback',
					'refColumns'        => 'id_status_feed'
			)
        );

}