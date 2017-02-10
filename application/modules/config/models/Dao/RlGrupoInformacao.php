<?php

class Config_Model_Dao_RlGrupoInformacao extends App_Model_Dao_Abstract
{
    protected $_name          = "rl_grupo_informacao";
    protected $_primary       = "id";
    protected $_rowClass = 'Config_Model_Vo_RlGrupoInformacao';

    public function listGrupoInfo($id_grupo = null, $id_info = null)
    {
        $select = $this->select()->from(array('c' => $this->_name));

        if(!empty($id_info)){
            $select->where('c.id_info = ?', $id_info);
        }
        if(!empty($id_grupo)){
            $select->where('c.id_grupo = ?', $id_grupo);
        }

        return $this->fetchAll($select)->toArray();
    }
}