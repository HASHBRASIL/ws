<?php

class Config_Model_Dao_RlPerfilInformacao extends App_Model_Dao_Abstract
{
	protected $_name          = "rl_perfil_informacao";
	protected $_primary       = "id";
	protected $_rowClass = 'Config_Model_Vo_RlPerfilInformacao';

    public function getByInformacao($idtinf) {
         $select = $this->select()
                       ->from(array($this->_name))
                       ->where('id_informacao = ?',$idtinf);
        return $this->fetchAll($select)->toArray();
    }

    public function getByInformacaoMultiplo($idtinf) {
         $select = $this->select()
                       ->from(array($this->_name))
                       ->where('id_informacao = ?',$idtinf)
                       ->where('multiplo = true');
        return $this->fetchAll($select)->toArray();
    }

    public function getByPerfisMultiplo($strPerfis) {
        $qry = $this->_db->prepare("SELECT
                                        p.id,
                                        tinfo.ordem,
                                        rpi.id,
                                        p.metanome AS perfil,
                                        p.nome AS perfil_nome,
                                        p.descricao AS perfil_descricao,    
                                        rpi.obrigatorio,
                                        rpi.multiplo,
                                        rpi.pesquisa,
                                        rpi.filtro,
                                        rpi.lista,
                                        tinfo.id as id,
                                        tinfo.nome as nome,
                                        tinfo.tipo as tipo,
                                        tinfo.mascara as mascara,
                                        tinfo.descricao as descricao,
                                        tinfo.metanome as metanome,
                                        tinfo.id_pai,
                                        tinfo.tamanho,
                                        infopai.nome as nome_pai,
                                        infopai.descricao as descricao_pai
                                        ,json_object(array_agg(tinfom.metanome), array_agg(tinfom.valor)) as metadatas
                                    FROM tp_informacao tinfo
                                    JOIN rl_perfil_informacao rpi ON ( tinfo.id = rpi.id_informacao )
                                    JOIN tb_perfil p              ON ( rpi.id_perfil = p.id )
                                    left outer JOIN tp_informacao infopai    ON ( tinfo.id_pai = infopai.id )
                                    left outer JOIN tp_informacao_metadata tinfom       ON ( tinfo.id = tinfom.id_tpinfo )
                                    WHERE p.metanome = ANY(string_to_array(:strPerfis,','))
                                    GROUP BY rpi.id, p.id, tinfo.ordem, tinfo.id, infopai.nome, infopai.descricao
                                    ORDER by p.id, tinfo.ordem");
        $qry->bindParam(':strPerfis',$strPerfis);

        $qry->execute();

        $ret = $qry->fetchAll();

        return $ret;
    }
}