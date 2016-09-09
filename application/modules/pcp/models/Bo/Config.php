<?php
class Pcp_Model_Bo_Config extends App_Model_Bo_Abstract
{
    /**
     * @var Pcp_Model_Dao_Config
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Pcp_Model_Dao_Config();
        parent::__construct();
    }

}