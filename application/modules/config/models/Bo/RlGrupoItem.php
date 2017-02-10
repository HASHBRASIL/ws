<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Bo_RlGrupoItem extends App_Model_Bo_Abstract
{
    /**
     * @var Config_Model_Dao_ItemBiblioteca
     */
    protected $_dao;

    /**
     * @var integer
     */
    public function __construct()
    {
            $this->_dao = new Config_Model_Dao_RlGrupoItem();
            parent::__construct();
    }

    public function listGrupoItem($id_grupo = null, $id_item = null)
    {
        return $this->_dao->listGrupoItem($id_grupo, $id_item);
    }
}