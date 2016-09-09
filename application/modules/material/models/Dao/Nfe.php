<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  30/04/2013
 */
class Material_Model_Dao_Nfe extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gm_nfe";
    protected $_primary  = "id_nfe";

    protected $_rowClass = 'Material_Model_Vo_Nfe';

    protected $_dependentTables = array('Material_Model_Dao_Estoque');

    protected $_referenceMap    = array(
            'Imposto' => array(
                    'columns'           => 'id_imposto',
                    'refTableClass'     => 'Material_Model_Dao_Imposto',
                    'refColumns'        => 'id_imposto'
            ),
            'Transportador' => array(
                    'columns'           => 'id_transportador',
                    'refTableClass'     => 'Material_Model_Dao_Transportador',
                    'refColumns'        => 'id_transportador'
            ),
            'Endereco transportador' => array(
                    'columns'           => 'id_endereco_transportador',
                    'refTableClass'     => 'Sis_Model_Dao_Endereco',
                    'refColumns'        => 'id'
            ),
            'Empresa Destinatario' => array(
                    'columns'           => 'id_empresa_destinatario',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            ),
            'Endereco Destinatario' => array(
                    'columns'           => 'id_endereco_destinatario',
                    'refTableClass'     => 'Sis_Model_Dao_Endereco',
                    'refColumns'        => 'id'
            ),
            'Fornecedor' => array(
                    'columns'           => 'id_fornecedor',
                    'refTableClass'     => 'Empresa_Model_Dao_Empresa',
                    'refColumns'        => 'id'
            ),
            'Endereco fornecedor' => array(
                    'columns'           => 'id_endereco_fornecedor',
                    'refTableClass'     => 'Sis_Model_Dao_Endereco',
                    'refColumns'        => 'id'
            )
    );

    protected function _condPaginator(Zend_Db_Select $select)
    {
        $select->joinLeft(array('te' => 'tb_empresas'), 'te.id = id_fornecedor', array('emitente_empresa' => 'nome_razao'));
        $select->where($this->_name.'.ativo = ?', App_Model_Dao_Abstract::ATIVO);

        $workspaceSession = new Zend_Session_Namespace('workspace');
        if (!$workspaceSession->free_access){
            $select->where("{$this->_name}.id_workspace = {$workspaceSession->id_workspace} or {$this->_name}.id_workspace IS NULL");
        }
        $select->order("{$this->_name}.dt_criacao DESC");
    }

}