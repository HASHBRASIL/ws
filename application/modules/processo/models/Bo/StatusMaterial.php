<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  12/08/2013
 */
class Processo_Model_Bo_StatusMaterial extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_StatusMaterial
     */
    protected $_dao;

    const ESTIMADO       = 1;
    const VALIDADO       = 2;
    const BAIXA_PARCIAL  = 3;
    const BAIXA_TOTAL    = 4;
    const CANCELADO		 = 5;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_StatusMaterial();
        parent::__construct();
    }

}