<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Processo_Model_Bo_TamPapel extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_TamPapel
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_TamPapel();
        parent::__construct();
    }

}