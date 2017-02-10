<?php

class Auxiliar_Model_Dao_Ocupacao extends App_Model_Dao_Abstract
{
	protected $_name          = "cbo_ocupacao";
	protected $_primary       = "codigo";
	
	protected $_rowClass = 'Auxiliar_Model_Vo_Ocupacao';
}