<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  01/08/2013
 */
class Processo_Model_Bo_PcpTimer extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_PcpTimer
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_PcpTimer();
        parent::__construct();
    }

    public function getTimeByProcesso($cod_pro)
    {
        $list['list']          = $this->_dao->getTimeByProcesso($cod_pro);
        $list['total_hora']    = $this->_dao->getSumTimeByProcesso($cod_pro);
        return $list;
    }

}