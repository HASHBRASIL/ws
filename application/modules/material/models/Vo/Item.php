<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  27/05/2013
 */
class Material_Model_Vo_Item extends App_Model_Vo_Row
{

    public function getUnidadeCompra()
    {
        return $this->findParentRow('Sis_Model_Dao_TipoUnidade', 'Unidade Compra');
    }

    public function getUnidadeConsumo()
    {
        return $this->findParentRow('Sis_Model_Dao_TipoUnidade', 'Unidade Consumo');
    }

    public function getSumEstoque($filterWorkspace = true)
    {
        $estoqueBo         = new Material_Model_Bo_Estoque();
        $workspaceSession  = new Zend_Session_Namespace('workspace');

        $idWorkspace = null;
        if ($workspaceSession->free_access != true && $filterWorkspace)
            $idWorkspace = $workspaceSession->id_workspace;

        return $estoqueBo->sumItemEstoque($this->id_item,null, $idWorkspace);
    }
}