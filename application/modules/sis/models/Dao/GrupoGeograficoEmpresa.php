<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/07/2013
 */
class Sis_Model_Dao_GrupoGeograficoEmpresa extends App_Model_Dao_Abstract
{
    protected $_name = "ta_grupo_geografico_x_empresas";
    protected $_primary = array("id_grupo_geografico", 'id_empresa');

    public function deleteByEmpresa($idEmpresa)
    {
        $where = array("id_empresa = ?" => $idEmpresa);
        return $this->delete($where);
    }

    public function getIdGrupoByEmpresa($idEmpresa)
    {
        $select = $this->_db->select();
        $select->from($this->_name, 'id_grupo_geografico')
        ->where('id_empresa = ?', $idEmpresa);

        return $this->_db->fetchCol($select);
    }
}