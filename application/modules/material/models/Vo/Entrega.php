<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  15/05/2013
 */
class Material_Model_Vo_Entrega extends App_Model_Vo_Row
{

    public function getStatus()
    {
        return $this->findParentRow('Material_Model_Dao_Status', 'Status');
    }
}