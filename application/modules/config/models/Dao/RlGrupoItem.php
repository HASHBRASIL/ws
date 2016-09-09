<?php
/**
 * @author Eric de Castro
 * @since  08/03/2016
 */
class Config_Model_Dao_RlGrupoItem extends App_Model_Dao_Abstract
{
	protected $_name          = "rl_grupo_item";
	protected $_primary       = "id";

	protected $_rowClass = 'Config_Model_Vo_RlGrupoItem';



    public function listGrupoItem($id_grupo = null, $id_item = null)
    {
        $select = $this->select()
                       ->from(array('c' => $this->_name));

        if(!empty($id_item)){
            $select->where('c.id_item = ?', $id_item);
        }
        if(!empty($id_grupo)){
            $select->where('c.id_grupo = ?', $id_grupo);
        }
        return $this->fetchAll($select)->toArray();
    }
}