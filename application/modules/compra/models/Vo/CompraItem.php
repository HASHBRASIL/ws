<?php
/**
 * @author Vinicius Leônidas
* @since 24/10/2013
*/
class Compra_Model_Vo_CompraItem extends App_Model_Vo_Row
{
	public function getItem()
	{
		return $this->findParentRow('Material_Model_Dao_Item','Item');
	}

}