<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  21/05/2013
 */
class Sis_Model_Bo_Controle extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Controle
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Controle();
        parent::__construct();
    }

    public function deleteByProtocoloMaterial($idProtocolo, $tpControle)
    {
        $criteria = array('id_gm_protocolo = ?' => $idProtocolo, 'id_tp_controle = ?' => $tpControle);
        $this->_dao->delete($criteria);
    }

    public function deleteByProtocoloServico($idProtocolo, $tpControle)
    {
        $criteria = array('id_gs_protocolo = ?' => $idProtocolo, 'id_tp_controle = ?' => $tpControle);
        $this->_dao->delete($criteria);
    }

}