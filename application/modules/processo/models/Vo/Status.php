<?php
class Processo_Model_Vo_Status extends App_Model_Vo_Row
{

    public function getWorkspace()
    {
        return $this->findParentRow('Auth_Model_Dao_Workspace', 'Workspace');
    }

}