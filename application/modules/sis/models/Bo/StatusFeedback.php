<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/06/2014
 */
class Sis_Model_Bo_StatusFeedback extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_StatusFeedback
     */
    protected $_dao;

    const ABERTO          = 1;
    const DESENVOLVIMENTO = 2;
    const FECHADO         = 3;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_StatusFeedback();
        parent::__construct();
    }

}