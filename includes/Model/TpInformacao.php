<?php

/**
 * Created by PhpStorm.
 * User: solbisio
 * Date: 1/12/15
 * Time: 17:29
 */
class TpInformacao extends Base
{
    /**
     * Identificação do metadado de e-mail.
     */
    const META_EMAIL = 'EMAIL';




    function getTpInformacaoByPerfis($perfis)
    {
        $arrPerfis  =   explode(',',$perfis);
        $arrRetorno =   array();
        foreach ( $arrPerfis as $k => $perfil){
            $stmt = $this->dbh->prepare("SELECT
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
                                    WHERE p.metanome = ?
                                    GROUP BY rpi.id, p.id, tinfo.ordem, tinfo.id, infopai.nome, infopai.descricao
                                    ORDER by p.id, tinfo.ordem");

            $stmt->bindValue(1, $perfil);

            $stmt->execute();
            $rsTpInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ( $rsTpInfo as $k2 => $tpInf){
                $arrRetorno[]   =   $tpInf;
            }
        }
        return $arrRetorno;
    }

    function getTpInformacaoByPerfisByPessoaByGrupo($perfis, $idPessoa, $grupo, $check=false)
    {
        $qryAdd = "";
        if ($check) {
            $qryAdd = "and COALESCE (tbi2.valor, tbi.valor) is not null";
        }
        $stmt = $this->dbh->prepare("SELECT p.metanome AS perfil,
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
        $rsTpInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rsTpInfo;
    }

    function quemEhMeuPai( $id ){
        $stmt = $this->dbh->prepare("SELECT id_pai from tp_informacao WHERE id = :id");
        $stmt->bindValue(':id',         $id);
        $stmt->execute();

        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ( isset($rs[0]) && (!is_null($rs[0]['id_pai']))){
            return $rs[0]['id_pai'];
        } else {
            return false;
        }

    }

    function getByMetanome($metanome) {
        $stmt = $this->dbh->prepare("select * from  tp_informacao where metanome = ? ");
        $stmt->bindValue(1, $metanome);
        $stmt->execute();

        $rs =   $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rs[0];

    }

    function getById($id) {
        $stmt = $this->dbh->prepare("select * from  tp_informacao where id = ? ");
        $stmt->bindValue(1, $id);
        $stmt->execute();

        $rs =   $stmt->fetchAll(PDO::FETCH_ASSOC);
        return current($rs);
    }
}
