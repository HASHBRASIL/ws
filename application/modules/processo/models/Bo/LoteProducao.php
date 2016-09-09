<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Processo_Model_Bo_LoteProducao extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_LoteProducao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_LoteProducao();
        parent::__construct();
    }

    public function idMaxByProcesso($idProcesso)
    {
        return $this->_dao->idMaxByProcesso($idProcesso);
    }

    public function inativarByProcesso($idProcesso)
    {
        return $this->_dao->inativarByProcesso($idProcesso);
    }

}