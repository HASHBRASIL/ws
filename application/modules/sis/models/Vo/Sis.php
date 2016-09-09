<?php
/**
 * @author Carlos Vinicius Bonfim
 * @since 24/03/2013
 *
 */
class Sis_Model_Vo_Sis extends App_Model_Vo_Row
{

    public function getWorkspace()
    {
        return $this->findParentRow('Auth_Model_Dao_Workspace', 'Workspace');
    }
}