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

    public function getServicoEmFerramentas($metanome)
    {
        $query = 'with recursive servicosBusca AS (
                        with recursive servicos AS (
                    select * from tb_servico where id_pai is null AND metanome = \'GESTAODEFERRAMENTAS\'
                    union 
                               select tbs.* from tb_servico tbs
                               JOIN servicos s ON s.id_pai = tbs.id
                       ) select * from servicos where id_pai is null
                union 
                       select tbs2.* from tb_servico tbs2
                       JOIN servicosBusca s ON s.id = tbs2.id_pai
               ) SELECT * FROM servicosBusca WHERE metanome = ? LIMIT 1';
        
        
        return $this->_db->query($query, array($metanome))->fetch();
    }
    
    public function getServicoEmUmaArvore($id_servico, $metanome)
    {
        $query = 'with recursive servicosBusca AS (
                        with recursive servicos AS (
                    select * from tb_servico where id = ?
                    union 
                               select tbs.* from tb_servico tbs
                               JOIN servicos s ON s.id_pai = tbs.id
                       ) select * from servicos where id_pai is null
                union 
                       select tbs2.* from tb_servico tbs2
                       JOIN servicosBusca s ON s.id = tbs2.id_pai
               ) SELECT * FROM servicosBusca WHERE metanome = ? LIMIT 1';
        
        
        return $this->_db->query($query, array($id_servico, $metanome))->fetch();
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
    
    
    /*
     * @TODO PASSAR PARA O CMS.
     */
    public function listServicosPermitidos($id_pesssoa, $id_grupo, $params = array())
    {
        $part1 = '';
        $part2 = '';
        
        if(isset($params['visiveis'])){
            $part1 = ' AND ( rpg.dt_expiracao is null OR rpg.dt_expiracao > NOW()) AND s.visivel = true ';
            $part2 = ' WHERE ( rpg2.dt_expiracao is null OR rpg2.dt_expiracao > NOW()) AND s2.visivel = true ';
        }
        
        $select = $this->_db->prepare("WITH RECURSIVE getServices as (
                                                SELECT s.*
                                                FROM tb_servico s
                                                JOIN rl_permissao_pessoa rpg ON s.id = rpg.id_servico AND rpg.id_pessoa = ? AND rpg.id_grupo = ?
                                                WHERE s.id_pai is null 
                                                '.$part1.'
                                        UNION
                                                SELECT s2.*
                                                FROM tb_servico s2
                                                JOIN getServices gs ON s2.id_pai = gs.id
                                                JOIN rl_permissao_pessoa rpg2 ON s2.id = rpg2.id_servico AND rpg2.id_pessoa = ? AND rpg2.id_grupo = ?
                                                '.$part2.'
                                        ) SELECT getServices.*, ( SELECT json_object(array_agg(tsm.metanome), array_agg(tsm.valor)) FROM tb_servico_metadata tsm WHERE tsm.id_servico = getServices.id) as metas
                                        FROM getServices");
        
        $select->bindParam(1, $id_pesssoa);
        $select->bindParam(2, $id_grupo);
        $select->bindParam(3, $id_pesssoa);
        $select->bindParam(4, $id_grupo);

        $select->execute();
        return $select->fetch();
    }
}
