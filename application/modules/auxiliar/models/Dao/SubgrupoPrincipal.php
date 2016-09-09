<?php

class Auxiliar_Model_Dao_SubgrupoPrincipal extends App_Model_Dao_Abstract
{
	protected $_name          = "cbo_subgrupo_principal";
	protected $_primary       = "codigo";
	
	protected $_rowClass = 'Auxiliar_Model_Vo_SubgrupoPrincipal';
}