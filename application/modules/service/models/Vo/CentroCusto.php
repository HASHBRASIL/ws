<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  02/01/2014
 */
class Service_Model_Vo_CentroCusto extends App_Model_Vo_Row
{
    public function getPai()
    {
        return $this->findParentRow('Service_Model_Dao_CentroCusto', 'Pai');
    }
}