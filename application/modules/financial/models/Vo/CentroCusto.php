<?php
class Financial_Model_Vo_CentroCusto extends App_Model_Vo_Row
{
	public function getWorkspace()
	{
		return $this->findParentRow('Auth_Model_Dao_Workspace', 'Workspace');
	}
}