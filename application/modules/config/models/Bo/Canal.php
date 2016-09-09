<?php
/**
 * @author Fernando Augusto
 * @since  18/05/2016
 */
class Config_Model_Bo_Canal extends App_Model_Bo_Abstract
{
    /**
     * @var Config_Model_Dao_Grupo
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
        $this->_dao = new Config_Model_Dao_Canal();
        parent::__construct();
    }

    public function getCanalByMetanome( $metanome )
    {
        return $this->_dao->getCanalByMetanome( $metanome );
    }
}