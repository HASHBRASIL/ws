<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  12/08/2013
 */
class Processo_Model_Bo_TipoMaterial extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_TipoMaterial
     */
    protected $_dao;

    const PROPRIO = 1;
    const CLIENTE = 2;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_TipoMaterial();
        parent::__construct();
    }

}