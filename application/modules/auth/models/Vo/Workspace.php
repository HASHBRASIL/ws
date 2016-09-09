<?php
class Auth_Model_Vo_Workspace extends App_Model_Vo_Row
{
    public function getEmpresa()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa');
    }
}