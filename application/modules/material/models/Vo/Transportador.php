<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  03/05/2013
 */
class Material_Model_Vo_Transportador extends App_Model_Vo_Row
{

    public function getEmpresa()
    {
        return $this->findParentRow('Empresa_Model_Dao_Empresa', 'Empresa');
    }

    public function getRazao()
    {
        return $this->getEmpresa() ? $this->getEmpresa()->nome_razao : null;
    }

    public function getCnpj()
    {
        return $this->getEmpresa() ? $this->getEmpresa()->cnpj_cpf : null;
    }

    public function getEstadual()
    {
        return $this->getEmpresa() ? $this->getEmpresa()->estadual : null;
    }

}