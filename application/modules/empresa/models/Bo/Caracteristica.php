<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Empresa_Model_Bo_Caracteristica extends App_Model_Bo_Abstract
{
    /**
     * @var Empresa_Model_Dao_Caracterisca
     */
    protected $_dao;
    
    const FUNCIONARIO 	= 1;
    const FORNECEDOR	= 2;
    const GRUPO			= 3;
    const TRANSPORTADOR = 4;
    const DISTRIBUIDOR	= 5;
    const GERENTE		= 6;
    const COORDENARDOR	= 7;
    const CONSULTOR		= 8; 
		const SINDICATO = 10;
    
    public function __construct()
    {
        $this->_dao = new Empresa_Model_Dao_Caracteristica();
        parent::__construct();
    }
    
}