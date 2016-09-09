<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/07/2013
 */
class Sis_Model_Bo_GrupoGeografico extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_GrupoGeografico
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_GrupoGeografico();
        parent::__construct();
    }

}