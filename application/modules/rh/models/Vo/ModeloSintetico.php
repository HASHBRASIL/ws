<?php
class Rh_Model_Vo_ModeloSintetico extends App_Model_Vo_Row
{

    public function getEntrada()
    {
    	return $this->findParentRow('Rh_Model_Dao_EntradaSintetico', 'Entrada');
    }

    public function getNatureza()
    {
        return $this->findParentRow('Rh_Model_Dao_NaturezaSintetico', 'Natureza');
    }
}