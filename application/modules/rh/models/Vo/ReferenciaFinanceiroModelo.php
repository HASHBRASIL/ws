<?php
/**
 * @author Vinicius Leônidas
* @since 03/12/2013
*/
class Rh_Model_Vo_ReferenciaFinanceiroModelo extends App_Model_Vo_Row
{
	public function getModelo()
	{
		return $this->findParentRow('Rh_Model_Dao_ModeloSintetico', 'Modelo');
	}
}