<?php
/**
 * @author Vinicius Leônidas
 * @since 21/10/2013
 */
class Compra_Model_Vo_CampanhaItem extends App_Model_Vo_Row
{
    protected $_item;

	public function getItem()
	{
	    if(!$this->_item || $this->id_campanha && !$this->_item->id_item ){
	        $this->_item = $this->findParentRow('Material_Model_Dao_Item','Item');
	    }
	    return $this->_item;
	}
}