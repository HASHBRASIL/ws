<?php
/**
 * @author Vinicius Leônidas
 * @since 06/01/2014
 */
class Rh_Model_Dao_DocumentoIdentidade extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_documento_identidade';
	protected $_primary = 'id_rh_documento_identidade';
	
	protected $_referenceMap    = array(
	
			'Funcionario' => array(
					'columns'           => 'id_rh_funcionario',
					'refTableClass'     => 'Rh_Model_Dao_Funcionario',
					'refColumns'        => 'id_rh_funcionario'
			)
	);
}