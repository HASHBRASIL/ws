<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_RlGrupoServico extends App_Model_Dao_Abstract
{
	protected $_name          = "rl_grupo_servico";
	protected $_primary       = "id";
	//protected $_namePairs	  = "nome";

	protected $_rowClass = 'Config_Model_Vo_RlGrupoServico';

	//protected $_dependentTables = array('Financial_Model_Dao_RlGrupoServico');

    /**
     * @todo Substituir chamadas por self::listGrupoServico()
     */
    public function findAssociacao($idGrupo, $idServico){
        $select = $this->select()
            ->where('id_grupo = ?', $idGrupo)
            ->where('id_servico = ?', $idServico);

        return $this->fetchOne($select);
    }

    public function listGrupoServico($id_grupo = null, $id_servico = null)
    {
        $select = $this->select()
                       ->from(array('c' => $this->_name));

        if(!empty($id_servico)){
            $select->where('c.id_servico = ?', $id_servico);
        }
        if(!empty($id_grupo)){
            $select->where('c.id_grupo = ?', $id_grupo);
        }
        return $this->fetchAll($select)->toArray();
    }
}

