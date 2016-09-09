<?php
/**
 * @author Ellyson de Jesus Silva
* @since  22/04/2013
*/
class Sis_Model_Bo_Estado extends App_Model_Bo_Abstract
{
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_Estado();
        parent::__construct();
    }
}