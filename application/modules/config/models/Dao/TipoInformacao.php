<?php

class Config_Model_Dao_TipoInformacao extends App_Model_Dao_Abstract
{
    protected $_name          = "tp_informacao";
    protected $_primary       = "id";
    
    protected $_rowClass = 'Config_Model_Vo_TipoInformacao';

    public function getById($tib) {
        $select = $this->select()
                       ->from(array($this->_name))
                       ->where('id = ?',$tib);
        return $this->fetchAll($select)->toArray();
    }

    public function getByMetanome($metanome) {
        $select = $this->select()
                       ->from(array($this->_name))
                       ->where('metanome = ?',$metanome);
        return $this->fetchAll($select)->toArray();
    }

    public function getTpInformacaoByPerfisByPessoaByGrupo($perfis, $idPessoa, $grupo, $check=false)    {
        $qryAdd = "";
        if ($check) {
            $qryAdd = "and COALESCE (tbi2.valor, tbi.valor) is not null";
        }
        $stmt = $this->_db->prepare("SELECT p.metanome AS perfil, 
                                            p.descricao AS perfil_descricao, 
                                            rpi.obrigatorio, 
                                            rpi.multiplo, 
                                            rpi.pesquisa, 
                                            rpi.filtro, 
                                            rpi.lista,
                                            COALESCE(tinfo.id,        tinfo2.id) as id, 
                                            COALESCE(tinfo.nome,      tinfo2.nome) as nome, 
                                            COALESCE(tinfo.tipo,      tinfo2.tipo) as tipo, 
                                            COALESCE(tinfo.mascara,   tinfo2.mascara) as mascara, 
                                            COALESCE(tinfo.descricao, tinfo2.descricao) as descricao, 
                                            COALESCE(tinfo.metanome,  tinfo2.metanome) as metanome, 
                                            tinfo.id_pai,
                                            infopai.nome as nome_pai, 
                                            infopai.descricao as descricao_pai,  
                                            COALESCE (tbi2.valor, tbi.valor) as valor, 
                                            COALESCE (tbi2.id, tbi.id) as tbinfoid,
                                            COALESCE (tbi2.id_pai, tbi.id_pai) as tbinfopaiid,
                                            rlgi.id as rl_grupo_informacao_id
                                            ,json_object(array_agg(tinfom.metanome), array_agg(tinfom.valor)) as metadatas
                                    FROM tp_informacao tinfo
                                    JOIN rl_perfil_informacao rpi ON ( tinfo.id = rpi.id_informacao )
                                    JOIN tb_perfil p              ON ( rpi.id_perfil = p.id )
                                    left join tp_informacao tinfo2 on (tinfo2.id = tinfo.id_pai)
                                    left outer join tb_informacao tbi ON
                                      (tbi.id_tinfo = rpi.id_informacao AND tbi.id_pessoa = :idPessoa
                                        AND tbi.id in (SELECT id_info
                                                       FROM rl_grupo_informacao
                                                       WHERE id_pessoa = :idPessoa                                                     
                                                       AND id_grupo = ANY ((string_to_array(:grupos, ','))::uuid[])))

                                    left join tb_informacao tbi2 on (tbi2.id_pai = tbi.id and tinfo2.id = tbi2.id_tinfo)
                                    left join rl_grupo_informacao rlgi on rlgi.id_info = tbi.id
                                    left JOIN tp_informacao_metadata tinfom       ON ( tinfo.id = tinfom.id_tpinfo )
                                    left outer JOIN tp_informacao infopai         ON ( tinfo.id_pai = infopai.id )

                                    where p.metanome = ANY (string_to_array(:perfis, ','))
                                    {$qryAdd}
                                    group by p.metanome, p.descricao, rpi.obrigatorio, rpi.multiplo, rpi.pesquisa, rpi.filtro, rpi.lista, tinfo2.id, tinfo.id, tbi2.valor, tbi.valor, tbi2.id, tbi.id, tbi2.id_pai, tbi.id_pai, rlgi.id, infopai.nome, infopai.descricao
                                    order by p.metanome asc, tinfo.ordem asc, tinfo.id asc, tinfo2.ordem asc");

        $stmt->bindValue(':perfis', $perfis);
        $stmt->bindValue(':grupos', $grupo);
        $stmt->bindValue(':idPessoa', $idPessoa);

        $stmt->execute();
        $rsTpInfo = $stmt->fetchAll();

        return $rsTpInfo;
    }
}