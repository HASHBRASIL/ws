<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 14/12/15
 * Time: 21:26
 */
class Legacy_Model_Bo_ServicoMetadata extends App_Model_Bo_Abstract
{
    /**
     * Legacy_Model_Bo_ServicoMetadata constructor.
     */
    public function __construct()
    {
        $this->_dao = new Legacy_Model_Dao_ServicoMetadata();
        parent::__construct();
    }
}

