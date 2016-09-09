<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  16/04/2013
 */
class Material_Model_Bo_Grupo extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Grupo
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Grupo();
        parent::__construct();
    }

}