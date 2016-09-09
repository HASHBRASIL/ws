<?php
/**
 * @author Eric de Castro
 * @since  23/02/2016
 */
class Config_Model_Dao_Servico extends App_Model_Dao_Abstract
{
	protected $_name          = "tb_servico";
	protected $_primary       = "id";
	protected $_namePairs	  = "descricao";

	protected $_rowClass = 'Config_Model_Vo_Servico';

	//protected $_dependentTables = array('Financial_Model_Dao_Financial');

    /**
     * Busca serviços em base em um metanome, ou um conjunto de metanomes.
     *
     * @param string|string[] $metanome Metanome ou metanomes para consulta de serviços.
     * @return mixed[]
     */
    public function getServicoByMetanome($metanome) {
        $select = $this->select()
                       ->from(array($this->_name));

        is_array($metanome)
            ?$select->where('metanome IN (?)', $metanome)
            :$select->where('metanome = ?', $metanome);

        return $this->fetchAll($select)->toArray();
    }

    /**
     * Retorna uma lista de serviços, podendo ser recursiva.
     *
     * Caso seja uma lista recursiva, o pai também é retornado. Em uma lista
     * não recursiva, apenas os filhos são retornados.
     *
     * @param type $idPai
     * @param type $recursivo
     * @return type
     */
    public function getServicosFilhos($idPai, $recursivo = false)
    {
        if (!$recursivo) {
            $select = $this->select()
                ->from(array($this->_name));

            if (!is_null($idPai)) {
                $select->where('id_pai = ?', $idPai);
            } else {
                $select->where('id_pai IS NULL');
            }
            $select->order('nome');

            return $this->fetchAll($select)->toArray();
        }

        // -- Todos os filhos, e o pai incluso
        if (!is_null($idPai)) {
            $where = "WHERE id = ?";
        } else {
            $where = "WHERE id_pai IS NULL";
        }

        $query = <<<DML
WITH RECURSIVE ss AS (SELECT *
                        FROM tb_servico
                        {$where}
                      UNION SELECT s.*
                              FROM tb_servico s
                                INNER JOIN ss ON (ss.id = s.id_pai))
SELECT *
  FROM ss
  ORDER BY ss.nome
DML;

        $select = $this->_db->prepare($query);
        if (!is_null($idPai)) {
            $select->bindParam(1, $idPai);
        }
        $select->execute();

        return $select->fetchAll();
    }

    public function getModulosByGrupo($idGrupo)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['tsv' => $this->_name], 'tsv.*')
            ->join(['rgs' => 'rl_grupo_servico'], 'tsv.id = rgs.id_servico', '')
            ->where('tsv.id_pai IS NULL')
            ->where('rgs.id_grupo = ?', $idGrupo);

        return $this->fetchAll($select)->toArray();
    }

    public function delServico($id)
    {
        $condicao = $this->getAdapter()->quoteInto('id = ?', $id);
        return $this->delete($condicao);
    }

    public function getServico($id)
    {
        $select = $this->_db->prepare("SELECT * FROM {$this->_name} WHERE id = ?");
        $select->bindParam(1, $id);

        $select->execute();
        return $select->fetch();
    }
}
