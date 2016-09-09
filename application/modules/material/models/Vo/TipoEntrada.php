<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  09/06/2014
 */
class Material_Model_Vo_TipoEntrada extends App_Model_Vo_Row
{

    public function getMovimento()
    {
        return $this->findParentRow('Material_Model_Dao_TipoMovimento', 'Movimento');
    }
}