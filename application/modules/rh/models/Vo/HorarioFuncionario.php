<?php
class Rh_Model_Vo_HorarioFuncionario extends App_Model_Vo_Row
{

    public function getFuncionario()
    {
    	return $this->findParentRow('Rh_Model_Dao_Funcionario', 'Funcionario');
    }
    
}