<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/04/2013
 */
class Sis_Model_Bo_TipoSegmento extends App_Model_Bo_Abstract
{
    /**
     * @var Sis_Model_Dao_TipoSegmento
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Sis_Model_Dao_TipoSegmento();
        parent::__construct();
    }

}