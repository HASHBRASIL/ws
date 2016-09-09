<?php
/**
 * @author: Ellyson de Jesus Silva
 * @since: 02/07/2013
 */
class Service_Model_Vo_Orcamento extends App_Model_Vo_Row
{
    public function getTipoOrcamento()
    {
        return $this->findParentRow('Service_Model_Dao_TipoOrcamento', 'Tipo Orcamento');
    }

    public function getCliente()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Cliente');
    }
}