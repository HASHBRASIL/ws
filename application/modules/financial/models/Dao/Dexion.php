<?php

class Financial_Model_Dao_Dexion extends App_Model_Dao_Abstract
{
    protected $_name          = "ing_dexion_livrorazao";
    protected $_primary       = "id";

    public function getSelectDexion($time) {

        $ret = $this->select()->setIntegrityCheck(false)
             ->from(array('dex'      => $this->_name), array('id','datalivro','lancamento','valordebito','valorcredito','debcred','contade','contapara','texto1'))
             ->where('dex.time = ?', $time);

        return $ret;
    }

}