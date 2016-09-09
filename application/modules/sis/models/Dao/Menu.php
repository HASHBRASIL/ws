<?php
class Sis_Model_Dao_Menu extends App_Model_Dao_Abstract
{
    protected $_name          = "tb_menu";
    protected $_primary       = "id";
    protected $_namePairs     = "nome";

    public function getMenuByModulo($idModulo)
    {
        $select = $this->_db->select();
        $select->from(array($this->_name))
               ->where("id_modulo = ?", $idModulo);

        return $this->_db->fetchAll($select);
    }
}