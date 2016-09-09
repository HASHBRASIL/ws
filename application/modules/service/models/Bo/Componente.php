<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 02/07/2013
 */
class Service_Model_Bo_Componente extends App_Model_Bo_Abstract
{
    /**
     * @var Service_Model_Dao_Componente
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Service_Model_Dao_Componente();
        parent::__construct();
    }

    public function getComponenteByOrcamento($id_orcamento)
    {
        return $this->_dao->getComponenteByOrcamento($id_orcamento);
    }

    public function getPairsSumComponente($id_orcamento)
    {
        return $this->_dao->getPairsSumComponente($id_orcamento);
    }

    public function getPairsSumTpServico($id_orcamento)
    {
        return $this->_dao->getPairsSumTpServico($id_orcamento);
    }
}