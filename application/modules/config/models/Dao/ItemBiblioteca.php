<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_ItemBiblioteca extends App_Model_Dao_Abstract
{
	protected $_name          = "tb_itembiblioteca";
	protected $_primary       = "id";
	
	protected $_rowClass = 'Config_Model_Vo_ItemBiblioteca';
}