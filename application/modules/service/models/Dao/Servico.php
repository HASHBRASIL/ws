<?php
/**
 * @author Ellyson de Jesus Silva
 * @since  08/04/2013
 */
class Service_Model_Dao_Servico extends App_Model_Dao_Abstract
{

    protected $_name        = "tb_gs_servico";
    protected $_primary     = "id_servico";

    protected $_rowClass = 'Service_Model_Vo_Servico';

    protected $_dependentTables = array('Service_Model_Dao_ValorServico');

    protected $_referenceMap    = array(
        'Grupo' => array(
            'columns'           => 'id_grupo',
            'refTableClass'     => 'Service_Model_Dao_Grupo',
            'refColumns'        => 'id_grupo'
        ),
    		'subGrupo' => array(
            'columns'           => 'id_subgrupo',
            'refTableClass'     => 'Service_Model_Dao_SubGrupo',
            'refColumns'        => 'id_subgrupo'
        ),
    		'Classe' => array(
    				'columns'           => 'id_classe',
    				'refTableClass'     => 'Service_Model_Dao_Classe',
    				'refColumns'        => 'id_classe'
    		));
    
    public function getAll($idGrupo = null, $idSubgrupo = null, $idClasse = null )
    {
        $select = $this->_db->select();
        $select->from(array('ts' => $this->_name), array(
                                                         'id_servico',
                                                         'nome'     => 'ts.nome',
                                                         'descricao'=>'ts.descricao',
                                                         'unidade'  => new Zend_Db_Expr('concat(ts.unidade, " " , tu.nome )')
         ))
               ->joinInner(array('tu' => 'tb_gs_tipo_unidade'), 'ts.id_tipo_unidade = tu.id_tipo_unidade', null)
               ->where('ts.ativo = ?', App_Model_Dao_Abstract::ATIVO);

        if(!empty($idGrupo)){
            $select->where('ts.id_grupo = ?', $idGrupo );
        }

        if(!empty($idSubgrupo)){
            $select->where('ts.id_subgrupo = ?', $idSubgrupo );
        }

        if(!empty($idClasse)){
            $select->where('ts.id_classe = ?', $idClasse );
        }

        return $this->_db->fetchAll($select);
    }

    /**
     * @desc Irá retornar somente a coluna pedida
     * @param String $colName
     * @return array
     */
    public function getCol($colName)
    {
        $select = $this->_db->select();
        $select->from($this->_name, $colName)
        ->where('ativo = ?', App_Model_Dao_Abstract::ATIVO);

        return $this->_db->fetchCol($select);
    }
}