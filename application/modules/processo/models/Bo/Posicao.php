<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Processo_Model_Bo_Posicao extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_Posicao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_Posicao();
        parent::__construct();
    }

}