<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  10/05/2013
 */
class Material_Model_Dao_Protocolo extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_protocolo";
    protected $_primary  = "id_protocolo";

    protected $_rowClass = 'Material_Model_Vo_Protocolo';

    protected $_dependentTables = array('Material_Model_Dao_Estoque', 'Material_Model_Dao_Movimento');

    protected $_referenceMap    = array(
            'Receptora' => array(
                    'columns'           => 'id_empresa_receptora',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            ),
            'Fornecedor' => array(
                    'columns'           => 'id_empresa_fornecedor',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            ),
            'Transportador' => array(
                    'columns'           => 'id_transportador',
                    'refTableClass'     => 'Material_Model_Dao_Transportador',
                    'refColumns'        => 'id_transportador'
            ),
            'Tipo Protocolo' => array(
                    'columns'          => 'id_tp_protocolo',
                    'refTableClass'    => 'Material_Model_Dao_TipoEntrada',
                    'refColumns'       => 'id_tp_protocolo'
            )
    );

    public function selectPaginator(array $options = null)
    {
        $select = $this->_db->select()->from(array('tp' => $this->_name))
        ->joinLeft(array('te' => 'tb_empresas'), 'tp.id_empresa_receptora = te.id', array('nome_receptor' => 'te.nome_razao'))
        ->joinLeft(array('tpro' => 'tb_processo'), 'tp.id_processo = tpro.pro_id', array('pro_nome' => new Zend_Db_Expr("CONCAT(pro_codigo,' - ',pro_desc_produto)")))
        ->joinInner(array('tpe' => 'tb_gm_tp_protocolo'), 'tp.id_tp_protocolo = tpe.id_tp_protocolo', array('nome_tp_protocolo' => 'tpe.nome'));

        $workspaceSession = new Zend_Session_Namespace('workspace');
        if (!$workspaceSession->free_access){
            $select->where("tp.id_workspace = {$workspaceSession->id_workspace} or tp.id_workspace IS NULL");
        }
        $this->_searchPaginator($select, $options);
        $this->_condPaginator($select);
        return $select;
    }

    protected function _condPaginator(Zend_Db_Select $select)
    {
        $select->where('tp.ativo = ?', App_Model_Dao_Abstract::ATIVO);
    }

    /*public function registroExistente(){

    }*/

}