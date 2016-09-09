<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  17/10/2013
 */
class Compra_Model_Bo_TipoComissao extends App_Model_Bo_Abstract
{
    /**
     * @var Compra_Model_Dao_TipoComissao
     */
    protected $_dao;

    public function __construct()
    {
        $this->_dao = new Compra_Model_Dao_TipoComissao();
        parent::__construct();
    }

}