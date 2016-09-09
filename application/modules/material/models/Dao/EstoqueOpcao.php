<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  14/01/2014
 */
class Material_Model_Dao_EstoqueOpcao extends App_Model_Dao_Abstract
{
    protected $_name     = "ta_gm_estoque_x_opcao";
    protected $_primary  = array("id_estoque", "id_opcao");

    protected $_referenceMap    = array(
            'Opcao' => array(
                    'columns'           => 'id_opcao',
                    'refTableClass'     => 'Material_Model_Dao_Opcao',
                    'refColumns'        => 'id_opcao'
            ),
            'Estoque' => array(
                    'columns'           => 'id_estoque',
                    'refTableClass'     => 'Material_Model_Dao_Estoque',
                    'refColumns'        => 'id_estoque'
            )
    );

    public function getListAll($idEstoque = null, $idOpcao = null)
    {
        $select = $this->_db->select();
        $select->from(array('teo' => $this->_name))
               ->joinInner(array('to' => 'tb_gm_opcao'), 'teo.id_opcao = to.id_opcao', array('nome_opcao' => 'nome'))
               ->joinInner(array('ta' => 'tb_gm_atributo'), 'to.id_atributo = ta.id_atributo', array('nome_atributo' => 'nome', 'id_atributo'))
               ->joinInner(array('te' => 'tb_gm_estoque'), 'teo.id_estoque = te.id_estoque');

        if($idEstoque)
            $select->where('teo.id_estoque = ?', $idEstoque);

        if($idOpcao)
            $select->where('teo.id_opcao = ?', $idOpcao);

        return $this->_db->fetchAll($select);
    }
}