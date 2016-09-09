<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  22/05/2013
 */
class Service_Model_Dao_Protocolo extends App_Model_Dao_Abstract
{
    protected $_name     = "tb_gs_protocolo";
    protected $_primary  = "id_protocolo";

    protected $_rowClass = 'Service_Model_Vo_Protocolo';

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
            )
    );

    public function buscaProtocolo()
    {
        $select = $this->_db->select()->from(array('tp' => $this->_name))
        ->joinLeft(array('te' => 'tb_empresas'), 'tp.id_empresa_receptora = te.id', array('nome_receptor' => 'te.nome_razao'))
        ->joinInner(array('tpe' => 'tb_gs_tp_entrada'), 'tp.id_tp_entrada = tpe.id_tp_entrada', array('nome_tp_entrada' => 'tpe.nome'));
        $select->where('tp.ativo = ?', App_Model_Dao_Abstract::ATIVO);
        return $this->_db->fetchAll($select);
    }
}