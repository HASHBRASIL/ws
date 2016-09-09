<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_InformacaoMetadata extends App_Model_Dao_Abstract
{
	protected $_name          = "tp_informacao_metadata";
	protected $_primary       = "id";
	
	protected $_rowClass = 'Config_Model_Vo_InformacaoMetadata';
}