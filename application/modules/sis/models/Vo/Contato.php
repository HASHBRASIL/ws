<?php
/**
 * @author Ellyson de Jesus Silva
 * @since 17/07/2013
 *
 */
class Sis_Model_Vo_Contato extends App_Model_Vo_Row
{

    public function getCargo()
    {
        return $this->findParentRow('Sis_Model_Dao_Cargo', 'Cargo');
    }

    public function getReferenciado()
    {
        return $this->findParentRow('Sis_Model_Dao_ContatoReferenciado', 'Contato Referenciado');
    }

    public function getDepartamento()
    {
        return $this->findParentRow('Sis_Model_Dao_ContatoDepartamento', 'Contato Departamento');
    }

    public function getMarketing()
    {
        return $this->findParentRow('Empresa_Model_Dao_MailMarketing', 'Marketing');
    }

}