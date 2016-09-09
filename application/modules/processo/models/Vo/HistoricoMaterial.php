<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  12/08/2013
 */
class Processo_Model_Vo_HistoricoMaterial extends App_Model_Vo_Row
{
	public function getUnidade()
	{
		return $this->findParentRow('Sis_Model_Dao_TipoUnidade', 'Unidade');
	}

	public function getMarca()
	{
		return $this->findParentRow('Material_Model_Dao_Marca', 'Marca');
	}

	public function getItem()
	{
	    return $this->findParentRow('Material_Model_Dao_Item', 'Item');
	}

	public function getTipoMaterial()
	{
	    return $this->findParentRow('Processo_Model_Dao_TipoMaterial', 'tipo Material');
	}

	public function getStatus()
	{
	    return $this->findParentRow('Processo_Model_Dao_StatusMaterial', 'Status');
	}

	public function getUsuario()
	{
	    return $this->findParentRow('Auth_Model_Dao_Usuario', 'Usuario');
	}
}