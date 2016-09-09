<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  13/05/2013
 */
class Material_Model_Bo_Status extends App_Model_Bo_Abstract
{
    /**
     * @var Material_Model_Dao_Status
     */
    protected $_dao;

    const PREP_ENVIO = 1;
    const ENVIADO    = 2;

    public function __construct()
    {
        $this->_dao = new Material_Model_Dao_Status();
        parent::__construct();
    }

}