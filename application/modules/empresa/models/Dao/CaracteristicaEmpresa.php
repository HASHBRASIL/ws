<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  23/07/2013
 */
class Empresa_Model_Dao_CaracteristicaEmpresa extends App_Model_Dao_Abstract
{
    protected $_name          = "ta_caracteristica_x_empresa";
    protected $_primary       = array('id_caracteristica', 'id_empresa');

    public function deleteByEmpresa($idEmpresa)
    {
        $where = array("id_empresa = ?" => $idEmpresa);
        return $this->delete($where);
    }

    public function getIdPerfilByEmpresa($idEmpresa)
    {
        $select = $this->_db->select();
        $select->from($this->_name, 'id_caracteristica')
               ->where('id_empresa = ?', $idEmpresa);

        return $this->_db->fetchCol($select);
    }

    public function deleteCaracteristica($idEmpresa, $idCaracteristica)
    {
        $where = array("id_empresa = ?" => $idEmpresa, 'id_caracteristica = ?' => $idCaracteristica);
        return $this->delete($where);
    }
}