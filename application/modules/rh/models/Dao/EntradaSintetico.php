<?php
/**
 * @author Vinicius Leônidas
 * @since 03/12/2013
 */
class Rh_Model_Dao_EntradaSintetico extends App_Model_Dao_Abstract
{
	protected $_name = 'tb_rh_entrada_sintetico';
	protected $_primary = 'id_rh_entrada_sintetico';
	
	protected $_dependentTables = array('Rh_Model_Dao_ModeloSintetico');
	
}