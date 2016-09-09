<?php
class Sis_Model_Bo_Modulo extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_Modulo
     */
     protected $_dao;

     public function __construct()
     {
         $this->_dao = new  Sis_Model_Dao_Modulo();
         parent::__construct();
     }
}