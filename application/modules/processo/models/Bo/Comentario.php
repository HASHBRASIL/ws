<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  25/11/2013
 */
class Processo_Model_Bo_Comentario extends App_Model_Bo_Abstract
{
    /**
     * @var Processo_Model_Dao_Comentario
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Processo_Model_Dao_Comentario();
        parent::__construct();
    }

}