<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  23/04/2013
 */
class Sis_Model_Bo_TipoPessoa extends App_Model_Bo_Abstract
{
    protected $_dao;

    const FISICA   = 2;
    const JURIDICA = 1;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_TipoPessoa();
        parent::__construct();
    }
}